<?php 

namespace khalango02\nwtosns;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient; 
use Exception;

class AwsSnsService
{
    public function sendMessage(
        string $message,
        string $phone,
        string $attribute = null,
        string $sender
    ): bool {
        try {
            $snSclient = $this->newSnsClient();

            $attribute = $this->getDefaultSmsType();

            if ($attribute == null){
                $attribute = 'Transactional';
            }

            $publish = [
                'Message' => $message,
                'PhoneNumber' => $phone,
                'SMSType' => $attribute,
                'SenderID' => $sender,
            ];

            $snSclient->publish($publish);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function newSnsClient(): SnsClient
    {
        return new SnsClient(
            config('aws')['sns']
        );
    }
    public function listOptoutNumbers()
    {
        try {
            $snSclient = $this->newSnsClient();

            $listNumbers = $snSclient->listPhoneNumbersOptedOut([]);
            var_dump($listNumbers);
        }catch (AwsException $e){
            error_log($e->getMessage());
        }
    }
    public function getDefaultSmsType()
    {
        try {
            $snSclient = $this->newSnsClient();

            $attType = $snSclient->getSmsAttributes([
                'attributes' => ['defaultSMSTyoe'],
            ]);

            return $attType;
        }catch (AwsException $e){
            error_log($e->getMessage());
        }
    }
    public function setSmsType(
        string $type
    ){
        try {
            $snsClient = $this->newSnsClient();

            $setSMS = $snsClient->SetSMSAttributes([
                'attributes' => [
                    'DefaultSMSType' => $type,
                ],
            ]);
            return($setSMS);
        }catch (AwsException $e){
            error_log($e->getMessage());
        }
    }

  /**
  * Send SMS for multiples phone numbers
  *
  * JSON sample
  *{
  *senddata: 
  *[
  *{"message":"XXXXXXXXXX", "PhoneNumber":+1123456789, "SMSType": "Transactional", "SenderID": "XXXX"},
  *{"message":"XXXXXXXXXX", "PhoneNumber":+1123456789, "SMSType": "Transactional", "SenderID": "XXXX"},
  *{"message":"XXXXXXXXXX", "PhoneNumber":+1123456789, "SMSType": "Transactional", "SenderID": "XXXX"},
  *]
  *}' 
  */
    public function SendMassiveSms($data)
    {
        try{
            $snsClient = $this->newSnsClient();

            $data = file_get_contents('php://input');
            $json = json_decode($data);
            $sendNumbers = $json->senddata;

            foreach ($sendNumbers as $e)
            {
                $publish = ['message' => $e->message,
                            'PhoneNumber' => $e->PhoneNumber,
                            'SMSType' => $e->SMSType,
                            'SenderID' => $e->SenderID
                ];

                $snsClient->publish($publish);

                return response()->json(['success'=>true,'message'=>$e->PhoneNumber],200);
            }
        }catch (AwsException $e){
                error_log($e->getMessage());

                return false;
        }
    }
}