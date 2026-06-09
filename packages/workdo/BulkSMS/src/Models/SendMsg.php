<?php

namespace Workdo\BulkSMS\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;


class SendMsg extends Model
{
   
public static function SendMsgs($mobile_no, $uArr,$content,$company_id = null)
    {
        $msg = self::replaceVariable($content, $uArr ,$company_id);
        $company_settings = getCompanyAllSetting();

        $bulksms_username = isset($company_settings['bulksms_username']) ? $company_settings['bulksms_username'] : '';
        $bulksms_password = isset($company_settings['bulksms_password']) ? $company_settings['bulksms_password'] : '';

        if ((!empty($bulksms_username)) && (!empty($bulksms_password))) {
            $url = "https://api.bulksms.com/v1/messages";

            $client = new Client();

            try {
                $response = $client->post($url, [
                    'auth' => [$bulksms_username, $bulksms_password],
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'to' => [$mobile_no],
                        'body' => $msg
                    ]
                ]);
                return json_decode($response->getBody(), true);
            } catch (RequestException $e) {
                return [
                    'error' => true,
                    'message' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage(),
                ];
            }
        } else {
            return false;
        }
    }
    public static function replaceVariable($content, $obj ,$company_id)
    {
        $arrVariable = [
            '{user_name}',
        ];

        $arrValue    = [
            'user_name' => '-',
        ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }

        $user = Auth::user();
        
        if(empty($user)){
            $user = User::find($company_id);
        }

        $arrValue['company_name'] = $user->name;

        return str_replace($arrVariable, array_values($arrValue), $content);
    }


}