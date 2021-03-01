<?
namespace App\VKBot;

class GenerateKeyboard {
    public $keyboard = [];

    function __construct($one_time, $inline, $buttons = []) {
        $this->keyboard['one_time'] = $one_time;
        $this->keyboard['inline'] = $inline;
        $this->keyboard['buttons'] = $buttons;
    }

    public function set_one_time($val) {
        $this->keyboard['one_time'] = $val;
    }

    public function set_inline($val) {
        $this->keyboard['inline'] = $val;
    }

    public function set_buttons($buttons) {
        $this->keyboard['buttons'] = $buttons;
    }

    public function button_text($label, $color, $payload = null) {
        $data = [
            'action' => [
                'type' => 'text',
                'label' => $label
            ],
            'color' => $color					
        ];

        if ($payload) {
            $data['action']['payload'] = json_encode($payload);
        }

        return $data;
    }

    public function button_link($label, $link, $payload = null) {
        $data = [
            'action' => [
                'type' => 'open_link',
                'link' => $link,
                'label' => $label
            ]				
        ];

        if ($payload) {
            $data['action']['payload'] = json_encode($payload);
        }

        return $data;
    }

    public function button_location($payload = null) {
        $data = [
            'action' => [
                'type' => 'location'
            ]				
        ];

        if ($payload) {
            $data['action']['payload'] = json_encode($payload);
        }

        return $data;
    }

    public function button_pay($label, $hash, $payload = null) {
        $data = [
            'action' => [
                'type' => 'vkpay',
                'label' => $label,
                'hash' => $hash
            ]				
        ];

        if ($payload) {
            $data['action']['payload'] = json_encode($payload);
        }

        return $data;
    }

    public function button_app($label, $app_id, $owner_id, $hash, $payload = null) {
        $data = [
            'action' => [
                'type' => 'open_app',
                'label' => $label,
                'app_id' => '',
                'owner_id' => '',
                'hash' => ''
            ]				
        ];

        if ($payload) {
            $data['action']['payload'] = json_encode($payload);
        }

        return $data;
    }

    public function button_callback($label, $color, $payload = null) {
        $data = [
            'action' => [
                'type' => 'callback',
                'label' => $label
            ],
            'color' => $color					
        ];

        if ($payload) {
            $data['action']['payload'] = json_encode($payload);
        }

        return $data;
    }

    public function set_row($id, $buttons) {
        $this->keyboard['buttons'][$id] = $buttons;
    }

    public function is_empty() {
        return empty($this->keyboard['buttons']);
    }

    public function clear() {
        $this->keyboard['buttons'] = [];
    }

    public function get() {
        return $this->keyboard;
    }
}