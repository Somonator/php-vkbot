<?
namespace App\Controllers;

use App\VKBot\Handlers\NewMessageHandler;

class VKBotController {
    private function run_event($event) {
        switch ($event['type']) {
            case 'confirmation':
                $this->return_response($GLOBALS['config']['vkbot_confirm']);
            break;
    
            case 'message_new':
                new NewMessageHandler($event['object']);
    
                $this->return_response('ok');
            break;
    
            default:
                $this->return_response('Unsupported event');
            break;
        }
        
        $this->return_response('ok');
    }
    
    private function return_response($data) {
        echo $data;
        exit;
    }

    public function init() {
        if (!isset($_REQUEST)) {
            exit;
        }
        
        $event = json_decode(file_get_contents('php://input'), true);
        
        if (isset($event['secret']) && $event['secret'] !== $GLOBALS['config']['vkbot_secret']) {
            exit;
        }

        if (isset($event['type'])) {
            $this->run_event($event);
        }
    }
}