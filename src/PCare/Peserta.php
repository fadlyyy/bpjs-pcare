<?php
namespace Barqdev\BpjsPcare\PCare;
use Barqdev\BpjsPcare\BpjsService;

class Peserta extends BpjsService
{
    /**
     * @param $keyword
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPeserta ($keyword) {
        $response = $this->get('peserta/' . $keyword);
        return json_decode($response, TRUE);
    }
    
    
    /**
     * @param $jnsKartu = ['nik', 'noka']
     * @param $keyword
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPesertaJenisKartu ($jnsKartu , $keyword) {
        $response = $this->get('peserta/' . $jnsKartu . '/' . $keyword);
        return json_decode($response, TRUE);
    }
}