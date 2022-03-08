<?php

namespace Bermuda\ErrorHandler;

use Throwable;
use Bermuda\HTTP\Contracts\ResponseFactoryAwareTrait;
use Bermuda\Validation\ValidationException;
use Bermuda\HTTP\Contracts\ResponseFactoryAwareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ValidationErrorResponseGenerator implements ErrorResponseGeneratorInterface, ResponseFactoryAwareInterface
{
    use ResponseFactoryAwareTrait;
    public function canGenerate(Throwable $e): bool
    {
        return $e instanceof ValidationException;
    }

    /**
     * @param Throwable $e
     * @param ServerRequestInterface|null $request
     * @return ResponseInterface
     * @throws Throwable
     */
    public function generateResponse(Throwable $e): ResponseInterface
    {
        if (!$this->canGenerate($e)) {
            throw $e;
        }

        /**
         * @var ValidationException $e;
         */
        $response = $this->responseFactory->createResponse(400);
        $response->getBody()->write(json_encode($e->getErrors()));

        return $response->withHeader('content-type', 'application/json');
    }
}
