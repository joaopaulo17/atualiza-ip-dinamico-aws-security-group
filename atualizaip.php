<?php
/**
  * Atualiza o security group da aws
  * Se o mesmo ainda não estiver nas politicas
  *
  * Script baseado no tutorial abaixo
  * http://www.ajginteractive.com/blog/keep-your-aws-ec2-security-group-updated-with-a-dynamic-ip-address/
  *
  * @param author	João Paulo Marques
  * @param e-mail	joaopaulo17@gmail.com
  * @param create-date	2020-29-03
  * @param modify-date  2020-31-03
  */

// Importações
require 'config/configuracoes.php';
require 'vendor/autoload.php';

use Aws\Ec2\Ec2Client;
use Aws\Exception\AwsException;


class atualizaip
{

    private $frontPort;
    private $toPort;
    private $protocolIP;

    public function __construct(int $frontPort, int $toPort, string $protocolIP){
        $this->setFrontPort($frontPort);
        $this->setToPort($toPort);
        $this->setProtocolIP($protocolIP);

    }

    private function setFrontPort($frontPort){
        $this->frontPort = $frontPort;
    }

    private function setToPort($toPort){
        $this->toPort = $toPort;
    }

    private function setProtocolIP($protocolIP){
        $this->protocolIP = $protocolIP;
    }

    private function getFrontPort(){
        return $this->frontPort;
    }

    private function getToPort(){
        return $this->toPort;
    }

    private function getProtocolIP(){
        return $this->protocolIP;
    }




    /** 
     * * Função para tentar atualizar seu ip na security group
     * * @access public 
     * * @param bool $acessoLocal 
     * */
    public function atualizaIP(bool $acessoLocal = true)
    {
        if(!$this->validaConfiguracao()){
            throw new \Exception('Erro na configuração da aplicação');
        }
    
        try {

    
            // entra com as credenciais da AWS
            $credentials = new Aws\Credentials\Credentials(AWS_KEY, AWS_SECRET_KEY);

            $ec2Client = new Aws\Ec2\Ec2Client([
                'region' => REGION,
                'version' => '2016-11-15',
                'credentials' => $credentials,
                'scheme' =>'http'
            ]);
                    
            //Detalha o security group setado 
            $result = $ec2Client->DescribeSecurityGroups([
                'GroupIds' => [
                    SECURITY_GROUP_ID,
                    ]	
                ]	
            );


            //Pega todos os ips do security group e preenche um array
            foreach ($result['SecurityGroups']['0']['IpPermissions'] as $policy) {
                foreach ($policy['IpRanges'] as $listedIP) {
                    $ipRange[] = explode("/", $listedIP['CidrIp'])['0'];
                }			 
            }

            $externalIp = $this->getExternalIP($acessoLocal);
                    
            // Verificar se o ip atual do computador esta na listagem, se estiver termina a execução
            if(in_array($externalIp, $ipRange)){
                echo('Seu ip já esta na lista do security group');
                die();
            }else{
                // caso não estaja adicinona o memso
                $result = $ec2Client->authorizeSecurityGroupIngress([
                    'GroupId' => SECURITY_GROUP_ID,
                    'IpPermissions' => [
                        [
                            'FromPort' => $this->getFrontPort(),   // -1 all ports
                            'IpProtocol' => $this->getProtocolIP(),
                            'IpRanges' => [
                                [
                                    'CidrIp' => $externalIp . '/32',
                                    'Description' => $externalIp . ' --- ' . date("m-d-Y H:i:s"),
                                ],
                            ],
                            'ToPort' => $this->getToPort(),
                        ],
                    ],
                ]);
                echo('Seu ip foi adicionado ao security group');
                die();

            } 
            unset($ipRange);
        } catch (AwsException $e2) {
            echo 'Exceção AWS: ',  $e2->getMessage(), "\n\n<br>";
        } catch (Exception $e) {
            echo 'Exceção PHP: ',  $e->getMessage(), "\n\n<br>";
        }
        

    }

     /** 
     * * Função para pegar o ip da requisição ou local
     * * @access private 
     * * @param bool $acesso 
     * * @return String
     * */
    private function getExternalIP($acesso): String{
        if($acesso){
            // Recupera o IP externo da conexão
            $expressaoRemoveDadosIp = '/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/';
            $externalContent = file_get_contents('http://checkip.dyndns.com/');
            preg_match($expressaoRemoveDadosIp, $externalContent, $externalIp );
            return $externalIp[1];
        }else{
            return $this->getClientIpEnv();
        }
    }


     /** 
     * * Valida se a configuração do sistema foi preenchida
     * * @access private 
     * * @param 
     * * @return bool
     * */
    private function validaConfiguracao(): bool{
        if(empty(AWS_KEY) || empty(AWS_SECRET_KEY) || empty(REGION) || empty(SECURITY_GROUP_ID))
            return false;
        else
            return true;
    }


    /** 
     * * Pega o ip da requisição feita
     * * @access private 
     * * @param 
     * * @return String
     * */
    private function getClientIpEnv():String {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
     
        return $ipaddress;
    }
}