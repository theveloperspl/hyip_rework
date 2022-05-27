<?php

namespace App\TelegramCommands;

use App\Models\TelegramSettings;
use App\Services\TelegramService;
use Illuminate\Cache\RateLimiter;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;

class StartCommand extends Command
{
    private const MAX_ATTEMPTS = 3;
    private const RETRY_SECONDS = 15 * 60;

    private TelegramService $telegramService;
    private RateLimiter $rateLimiter;

    public function __construct(TelegramService $telegramService, RateLimiter $rateLimiter)
    {
        $this->telegramService = $telegramService;
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Command name
     *
     * @var string
     */
    protected $name = 'start';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Handle investment platform account connection with Telegram profile';

    /**
     * Command arguments object decoding pattern (/command {foo} {bar}) that can be later accessed and processed
     *
     * @var string
     */
    protected $pattern = '{token}';

    /**
     * Process command
     *
     * @return void
     */
    public function handle()
    {
        $message = ':no_entry: Connection token is missing or incorrect, please try again';
        $keyboard = null;
        //make typing effect
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        //process command
        $arguments = (object)$this->getArguments();

        if (isset($arguments->token)) {
            $sender = $this->update->message->from->id;
            $cacheKey = $sender . ':connect_telegram';
            $token = $arguments->token;

            //limit request to prevent brute force attacks
            if ($this->rateLimiter->tooManyAttempts($cacheKey, self::MAX_ATTEMPTS)) {
                $minutes = gmdate('i:s', $this->rateLimiter->availableIn($cacheKey));
                $message = __('errors.throttled', ['minutes' => $minutes]);
            } else {
                $tokenCheck = TelegramSettings::whereConnectionToken($token)->first();
                if ($tokenCheck) {
                    $accountCheck = TelegramSettings::whereAccount($sender)->first();
                    if ($accountCheck) {
                        $message = __('telegram.already');
                    } else {
                        $this->telegramService->setUserTelegramAccount($tokenCheck->user, $sender);
                        $this->telegramService->setUserConnectionToken($tokenCheck->user, '');
                        $message = __('telegram.connected', ['username' => $tokenCheck->user->username, 'name' => config('app.name')]);
                        $keyboard = getEmojifiedTelegramKeyboard();
                    }

                }
                //add try to limiter
                $this->rateLimiter->hit($cacheKey, self::RETRY_SECONDS);
            }
        }

        //process all emojis in text for icons
        $message = Emojify::text($message);

        return $this->replyWithMessage([
            'text' => $message,
            'parse_mode' => 'markdown',
            'reply_markup' => $keyboard
        ]);
    }
}
