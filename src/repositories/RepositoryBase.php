<?php

namespace Tibelian\GangaPhoneApi\Repository;

/**
 * Access database query manager
 */
class RepositoryBase {

    // query temp container
    private string $lastQuery = "";

    // error temp container
    private string $error = "";

    /**
     * concatenate error message to 
     * the error container variable
     */
    protected function addErrorLog(string $err):void 
    {
        $this->error .= $err;
    }

    /**
     * concatenate query to the
     * query container variable
     */
    protected function addQueryLog(string $sql):void 
    {
        $this->lastQuery .= "\n######";
        $this->lastQuery .= trim($sql);
        $this->lastQuery .= "\n######";
    }

    /**
     * obtain the error container content
     */
    public function getErrorLog():string 
    {
        return $this->error;
    }

    /**
     * obtain the query container content
     */
    public function getQueryLog():string 
    {
        return $this->lastQuery;
    }

}