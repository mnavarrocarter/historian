<?php
declare(strict_types=1);

use Historian\EventSourcing\EventSourcedAggregateRepository;
use Historian\EventStore\EventSerializer\JsonEventSerializer;
use Historian\EventStore\PersistentEventStore;
use Historian\EventStore\StorageDriver\FilesystemStorageDriver;
use Historian\EventStore\StreamExtractor\AggregateClassStreamExtractor;
use Historian\EventStore\StreamExtractor\ChainStreamExtractor;
use Historian\EventStore\StreamExtractor\EventNameStreamExtractor;
use Historian\Util\ClosurePropertyAccessor;
use function Historian\uuid4;

require_once __DIR__.'/aggregate.php';

$person1 = uuid4();
$person2 = uuid4();
$person3 = uuid4();

$building = Building::create();
$building->registerPersonEntrance($person1);
$building->registerPersonEntrance($person2);
$building->registerPersonEntrance($person3);
$building->registerPersonEntrance($person3);
$building->registerPersonExit($person3);
$building->registerPersonExit($person3);
$building->registerPersonExit($person2);

// We create the Event Store
$driver = new FilesystemStorageDriver(sys_get_temp_dir().'/event-store');
$serializer = new JsonEventSerializer();
$streamExtractor = new ChainStreamExtractor();
$streamExtractor->push(new AggregateClassStreamExtractor());
$streamExtractor->push(new EventNameStreamExtractor());
$eventStore = new PersistentEventStore($driver, $serializer, $streamExtractor);

// We create the repo
$accessor = new ClosurePropertyAccessor();
$repo = new EventSourcedAggregateRepository($eventStore, $accessor);

$repo->saveAggregate($building);

$events = $eventStore->load('entrance-anomaly-detected');

dd(iterator_to_array($events));

/** @var Building $building */
//$building = $repo->findAggregate('7e96f610-32de-449e-8ef0-1d41e52dcad2');

dd($building);