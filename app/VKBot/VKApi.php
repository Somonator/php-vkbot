<?
namespace App\VKBot;

class VKApi {
    private $api_version = '5.73';
    private $api_endpoint = 'https://api.vk.com/method/';

    public function users_get($user_id) {
        return $this->api_call('users.get', [
            'user_id' => $user_id
        ]);
    }

    public function messages_send($peer_id, $message, $keyboard = [], $attachments = []) {
        $data = [
            'peer_id' => $peer_id,
            'message' => $message
        ];

        if (!empty($keyboard)) {
            $data['keyboard'] = json_encode($keyboard);
        }

        return $this->api_call('messages.send', $data);
    }

    private function api_call($method, $params = []) {
        $params['access_token'] = $GLOBALS['config']['vkbot_token'];
        $params['v'] = $this->api_version;

        $query = http_build_query($params);
        $url = $this->api_endpoint . $method;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        $error = curl_error($curl);
        
        if ($error) {
            $this->write_log($error);
            throw new \Exception('Failed ' . $method . ' request');
        }

        curl_close($curl);

        $response = json_decode($json, true);

        if (!$response || !isset($response['response'])) {
            $this->write_log($json);
            throw new \Exception('Invalid response for ' . $method . ' request');
        }

        return $response['response'];
    }

    private function write_log($error) {
        $trace = debug_backtrace();
        $function_name = isset($trace[2]) ? $trace[2]['function'] : '-';

        $write_str = date('H:i:s') . ' ';
        $write_str .= '[' . $function_name . ']' . ': ';
        $write_str .= $error;
        $write_str .= "\n" . "\n";

        $log_file = $GLOBALS['config']['logs_folder'] . '/vkbot.txt';


        if (file_exists($log_file)) {
            // 3 - message применяется к указанному в destination файлу. Перенос строки автоматически не добавляется в конец message.
            error_log($write_str, 3, $log_file);
        }
    }
}