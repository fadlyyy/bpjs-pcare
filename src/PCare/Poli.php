<?php
namespace Barqdev\BpjsPcare\PCare;
use Barqdev\BpjsPcare\BpjsService;

class Poli extends BpjsService
{
    /**
     * @param $start
     * @param $limit
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPoliFKTP ($start, $limit) {
        $response = $this->get('poli/fktp/' . $start . '/' . $limit);
        return json_decode($response, TRUE);
    }
}