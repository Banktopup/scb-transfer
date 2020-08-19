<?php
//https://github.com/Banktopup/banktopup-sdk-php


class Banktopup
{
    private $license;
    private $ch;
    private $deviceid;
    private $pin;
    private $account_no;

    private $BANKTOPUP_API = 'https://api-v1.banktopup.com';
    public function __construct($license = ""){
        $this->license = $license;
    }
    /**
     * @param mixed $deviceid
     */
    public function setDeviceid($deviceid)
    {
        $this->deviceid = $deviceid;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        if (strlen(trim($pin)) < 6){
            exit('Incomplete pin');
        }
        $this->pin = $pin;
    }

    /**
     * @param mixed $account_no
     */
    public function setAccountNo($account_no)
    {
        if (strlen(trim($account_no)) < 10){
            exit('Incomplete account number');
        }
        $this->account_no = $account_no;
    }

    public function Register($identification,$year,$month,$day,$pin,$mobile_phone_no,$account_no,$device_brand,$device_code){
        $this->setAccountNo($account_no);
        $this->setPin($pin);
        $data = [
            'identification' => $identification,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'mobile_phone_no' => $mobile_phone_no,
            'device_brand' => $device_brand,
            'device_code' => $device_code,
        ];

        return $this->API("POST","/api/v1/scb/register",$data);
    }
    public function ConfirmOTP($deviceid , $otp){
        $this->setDeviceid($deviceid);
        return $this->API("POST","/api/v1/scb/register/".$this->deviceid,["otp"=>$otp]);
    }
    public function CheckDevice($deviceid) {
        $this->setDeviceid($deviceid);
        return $this->API("POST","/api/v1/scb/check_device",[]);
    }
    public function Transactions($previous_day=  7,$page_number =1,$page_size = 20){
        return $this->API("POST","/api/v1/scb/transactions",[
            'previous_day' => $previous_day,
            'page_number' => $page_number,
            'page_size' => $page_size,
        ]);
    }
    public function Verification($account_to,$bank_code,$amount){
        return $this->API("POST","/api/v1/scb/verification",[
            'account_to' => $account_to,
            'bank_code' => $bank_code,
            'amount' => floatval($amount),
        ]);
    }
    public function Transfer($account_to,$bank_code,$amount){
        return $this->API("POST","/api/v1/scb/transfer",[
            'account_to' => $account_to,
            'bank_code' => $bank_code,
            'amount' => floatval($amount),
        ]);
    }
    public function Summary(){
        return $this->API("POST","/api/v1/scb/summary",[]);
    }
    public function Eligiblebanks(){
        return $this->API("POST","/api/v1/scb/eligiblebanks",[]);
    }
    public function Version(){
        return $this->API("POST","/api/v1/scb",[])['result']['version'];
    }
    private function API($method = "",$path = "", $data = []){

        $this->ch = curl_init();
        curl_setopt_array($this->ch, [
            CURLOPT_URL => $this->BANKTOPUP_API.$path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode(array_merge($data,[
                "deviceid" =>$this->deviceid,
                "pin"=>$this->pin,
                "account_no" => $this->account_no
            ])),
            CURLOPT_HTTPHEADER => [
                "x-auth-license: ".$this->license,
            ],
        ]);

        $response = curl_exec($this->ch);
        curl_close($this->ch);
        return json_decode($response,true);
    }
}