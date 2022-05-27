<?php

namespace Tibelian\GangaPhoneApi\Repository;

class RepositoryBase {

    private string $lastQuery = "";
    private string $error = "";

    protected function addErrorLog(string $err):void 
    {
        $this->error .= $err;
    }

    protected function addQueryLog(string $sql):void 
    {
        $this->lastQuery .= "\n######";
        $this->lastQuery .= trim($sql);
        $this->lastQuery .= "\n######";
    }

    public function getErrorLog():string 
    {
        return $this->error;
    }

    public function getQueryLog():string 
    {
        return $this->lastQuery;
    }

}