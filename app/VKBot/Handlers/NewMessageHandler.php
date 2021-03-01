<?
namespace App\VKBot\Handlers;

use App\VKBot\VKApi;
use App\VKBot\GenerateKeyboard;
//use App\Models\Faq;

class NewMessageHandler extends VKApi {
    var $f_per_row = 2; // колво рядов кнопок faq 1-5
    var $f_per_page = 6; // общее колво faq на страницу

    function __construct($data) {
        $this->init($data);
    }

    public function get_faq_answer($id) {
        $message = 'Информация не найдена';
        $post = faq::find($id);

        if ($post) {
            $message = $post->content;
        }

        return $message;
    }


    public function get_faq_buttons($page) {
        $posts = new Faq();
        $keyboard = new GenerateKeyboard(false, false);

        $per_row = $this->f_per_row;
        $per_page = $this->f_per_page;        

        $row_id = 0;  
        $offset_by_page = $page == 1 ? 0 : $per_page * $page - $per_page;
        
        $faq_count = $posts->count(); // всего записей в базе
        $faq = $posts->skip($offset_by_page)->take($per_page)->get()->toArray(); // записи с текущей страницы

        if (count($faq) > 0) {
            $row_id = 0;    
        
            for ($i = 0; $i < count($faq); ) {
                $row = array_slice($faq, $i, $per_row);
                $row = array_map(function($item) use ($keyboard) {
                    return $keyboard->button_text($item['title'], 'secondary', ['command' => 'faq_item', 'id' => $item['id']]);
                }, $row);
        
                $keyboard->set_row($row_id, $row);
                
                $i = $i + $per_row;
                $row_id++;
            }
        }

        $row_id++;

        if (count($faq) > 0 && $page > 1) {
            $keyboard->keyboard['buttons'][$row_id][] = $keyboard->button_text('Предыдущая страница', 'secondary', ['command' => 'faq_list', 'page_id' => $page - 1]);
        }  

        if (count($faq) > 0 && $page != ceil($faq_count / $per_page)) {
            $keyboard->keyboard['buttons'][$row_id][] = $keyboard->button_text('Следущая страница', 'secondary', ['command' => 'faq_list', 'page_id' => $page + 1]);
        }

        return $keyboard;
    }

    public function get_data_by_payload($payload) {
        $message = 'Неизвестная команда';
        $keyboard = new GenerateKeyboard(false, false);

        switch ($payload['command']) {
            case 'start':
                $message = 'Выберите действие';
                $keyboard->set_row(0, [
                    $keyboard->button_text('Часто задаваемые вопросы', 'secondary', ['command' => 'faq_list', 'page_id' => 1])
                ]);
                $keyboard->set_row(1, [
                    $keyboard->button_text('Обновить меню', 'secondary', ['command' => 'start'])
                ]);
            break;

            case 'faq_list':
                /*
                $page = isset($payload['page_id']) ? $payload['page_id'] : 1;

                $keyboard = $this->get_faq_buttons($page);

                if ($keyboard->is_empty()) {
                    $keyboard->set_row(0, [
                        $keyboard->button_text('Раздел пуст, вернуться в главное меню?', 'secondary', ['command' => 'start'])
                    ]);                  
                } else {
                    $keyboard->set_row(999, [
                        $keyboard->button_text('В главное меню', 'secondary', ['command' => 'start'])
                    ]);
                }
                */

                $message = 'Что хотите узнать?';

                $keyboard->set_row(0, [
                    $keyboard->button_text('Раздел пуст, вернуться в главное меню?', 'secondary', ['command' => 'start'])
                ]);   
            break;

            case 'faq_item':
                $message = isset($payload['id']) ? $this->get_faq_answer($payload['id']) : 'Информация не найдена';
            break;

            case 'reset':
                $message = 'Сейчас все верну как было';
                $keyboard->set_row(0, [
                    $keyboard->button_text('Начать', 'primary', ['command' => 'start'])
                ]);
            break;
        }

        return [
            'message' => $message,
            'keyboard' => !$keyboard->is_empty() ? $keyboard->get() : []
        ];
    }

    public function init($data) {
        if (isset($data['payload'])) {
            $payload_decode = json_decode($data['payload'], true);
            $p_data = $this->get_data_by_payload($payload_decode);
        } else {
            $p_data = $this->get_data_by_payload(['command' => 'start']);
        }

        $this->messages_send($data['user_id'], $p_data['message'], $p_data['keyboard']);
    }
}