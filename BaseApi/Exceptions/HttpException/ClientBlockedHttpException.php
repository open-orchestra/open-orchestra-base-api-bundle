<?php

namespace OpenOrchestra\BaseApi\Exceptions\HttpException;

/**
 * Class ClientBlockedHttpException
 */
class ClientBlockedHttpException extends ApiException
{
    const DEVELOPER_MESSAGE  = 'client.blocked';
    const HUMAN_MESSAGE      = 'open_orchestra_api.client.client_blocked';
    const STATUS_CODE        = '404';
    const ERROR_CODE         = 'x';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::STATUS_CODE, self::ERROR_CODE, self::DEVELOPER_MESSAGE, self::HUMAN_MESSAGE);
    }
}
