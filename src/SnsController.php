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
}