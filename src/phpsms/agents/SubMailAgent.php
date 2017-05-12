<?php

namespace Toplan\PhpSms;

/**
 * Class SubMailAgent
 *
 * @property string $appid
 * @property string $signature
 */
class SubMailAgent extends Agent implements TemplateSms, VoiceCode
{
    public function sendTemplateSms($to, $tempId, array $data, array $params)
    {
        $url = 'https://api.mysubmail.com/message/xsend.json';
        $params = array_merge($params, [
            'appid'     => $this->appid,
            'project'   => $tempId,
            'to'        => $to,
            'signature' => $this->signature,
            'vars'      => json_encode($data),
        ]);
        $result = $this->curl($url, $params, true);
        $this->setResult($result);
    }

    public function sendVoiceCode($to, $code, array $params)
    {
        $url = 'https://api.mysubmail.com/voice/verify.json';
        $params = array_merge($params, [
            'appid'     => $this->appid,
            'to'        => $to,
            'code'      => $code,
            'signature' => $this->signature,
        ]);
        $result = $this->curl($url, $params, true);
        $this->setResult($result);
    }

    protected function setResult($result)
    {
        if ($result['request']) {
            $this->result(Agent::INFO, $result['response']);
            $result = json_decode($result['response'], true);
            if ($result['status'] === 'success') {
                $this->result(Agent::SUCCESS, true);
            } else {
                $this->result(Agent::INFO, $result['msg']);
                $this->result(Agent::CODE, $result['code']);
            }
        } else {
            $this->result(Agent::INFO, 'request failed');
        }
    }
}
