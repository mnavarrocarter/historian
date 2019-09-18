<?php

namespace Historian\EventStore\Http;

use DateTime;
use DateTimeInterface;
use Exception;
use Historian\EventStore\Event;
use Historian\EventStore\EventStream;
use Historian\EventStore\ReadOnlyEventStore;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Next;

/**
 * Class ListEventsHandler
 *
 * This is a configurable handler that lists events.
 *
 * @author Matias Navarro Carter <mnavarro@option.cl>
 */
class ListEventsHandler implements MiddlewareInterface
{
    /**
     * @var ReadOnlyEventStore
     */
    private $store;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var array
     */
    private $config;

    /**
     * ListEventsHandler constructor.
     * @param ReadOnlyEventStore $store
     * @param ResponseFactoryInterface $responseFactory
     * @param array $config
     */
    public function __construct(ReadOnlyEventStore $store, ResponseFactoryInterface $responseFactory, array $config = [])
    {
        $this->store = $store;
        $this->responseFactory = $responseFactory;
        $this->config = array_merge([
            'until_param' => 'until',
            'default_stream' => 'master',
            'stream_param' => 'stream',
            'start_param' => 'start',
            'size_param' => 'size',
            'default_size' => null,
            'max_size' => null,
            'response_success_callable' => [$this, 'eventsResponse'],
            'response_error_callable' => [$this, 'errorResponse']
        ], $config);
    }

    /**
     * @param Request $request
     * @param Next $next
     * @return Response
     */
    public function process(Request $request, Next $next): Response
    {
        $params = $request->getQueryParams();
        $stream = $params[$this->config['stream_param']] ?? $this->config['default_stream'];
        $start = (int) ($params[$this->config['start_param']] ?? 0);
        $size = $params[$this->config['size_param']] ?? $this->config['default_size'];
        $size = $size !== null ? (int) $size : null;
        $until = $params[$this->config['until_param']] ?? null;

        // First we check if the stream exists.
        if (!$this->store->hasStream($stream)) {
            return $this->errorResponse(sprintf(
                'Stream "%s" does not exist',
                $stream
            ));
        }

        // Then we check if the max size was surpassed.
        if (is_int($size) && is_int($this->config['max_size']) && $size > $this->config['max_size']) {
            return $this->errorResponse(sprintf(
                'Maximum size of stream (%s) exceeded',
                $this->config['max_size']
            ));
        }

        $eventStream = $this->store->load($stream);
        $eventStream->start($start);
        if ($size !== null) {
            $eventStream->size($size);
        }
        if ($until !== null && ($date = $this->createDate($until))) {
            $eventStream->until($date);
        }

        return $this->eventsResponse($eventStream);
    }

    /**
     * @param string $error
     * @return Response
     */
    protected function errorResponse(string $error): Response
    {
        return $this->json([
            'msg' => $error,
            'status' => 400
        ], 400);
    }

    /**
     * @param EventStream $events
     * @return Response
     */
    protected function eventsResponse(EventStream $events): Response
    {
        $events->apply(static function (Event $event) {
            return $event->toArray();
        });
        return $this->json(iterator_to_array($events));
    }

    /**
     * @param array $data
     * @param int $status
     * @return Response
     */
    protected function json(array $data, $status = 200): Response
    {
        $response = $this->responseFactory->createResponse($status)
            ->withHeader('Content-Type', 'application/json; charset=utf8');
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    /**
     * @param string $until
     * @return DateTimeInterface|null
     */
    private function createDate(string $until): ?DateTimeInterface
    {
        try {
            return new DateTime($until);
        } catch (Exception $exception) {
            return null;
        }
    }
}