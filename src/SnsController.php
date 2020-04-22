<?php 

namespace khalango02\nwtosns;

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient; 
use Exception;

class AwsSnsService
{
    private $snsClient;

    public function __construct(
        array $config
    ) {
        $config['version'] = $config['version'] ?? 'latest';
        $this->snsClient = new snSClient($config);
    }
    
    public function sendMessage(
        string $message,
        string $phone,
        string $attribute = null,
        string $sender
    ): bool {
        try {
            $snSclient = $this->snsClient();

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
