<?php

namespace App\Classe;
use Mailjet\Client;
use \Mailjet\Resources;


class MailConfirmation{
    private $api_key = "35c14c7f8d62bcff3709f733e790ac5b";
    private $api_key_secret = "47ce6efd044cda70be04391705f612c6";

    
    public function send($to_email, $to_name, $subject, $content)
    {



       // $mj = new Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
       $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [ 
                    'From' => [
                        'Email' => "daouda.camara.0659841730@gmail.com",
                        'Name' => "DAILY QHSE"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    
                    
                    'TemplateID' => 5790380,
                    // 5789806
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        // $response->success() && dd($response->getData());
    
    }
}
