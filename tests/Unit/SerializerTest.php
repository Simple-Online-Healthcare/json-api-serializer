<?php

namespace Tests\Unit;

use Carbon\Carbon;
use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;
use SimpleOnlineHealthcare\JsonApi\Registry;
use SimpleOnlineHealthcare\JsonApi\Serializer;
use Tests\Concerns\Entities\Address;
use Tests\Concerns\Entities\User;
use Tests\Concerns\Normalizers\AddressNormalizer;
use Tests\Concerns\Normalizers\UserNormalizer;
use Tests\TestCase;

class SerializerTest extends TestCase
{
    protected Registry $registry;
    protected JsonApiSpecFactory $jsonApiSpecFactory;
    protected Serializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create some test config to be stored in the container
        $resourceTypeMapping = [
            User::class => 'users',
            Address::class => 'addresses',
        ];

        $normalizerMapping = [
            User::class => UserNormalizer::class,
            Address::class => AddressNormalizer::class,
        ];

        // Modify the service provider for the test
        $this->application->singleton(JsonApi::class, function () {
            return new JsonApi(config('json-api-serializer.jsonapi.version', '1.0'));
        });

        $this->application->singleton(Registry::class, function () use ($resourceTypeMapping, $normalizerMapping) {
            return new Registry($this->application, $resourceTypeMapping, $normalizerMapping);
        });

        $this->application->singleton(JsonApiSpecFactory::class, function () {
            return new JsonApiSpecFactory(new JsonApi('1.0'), new Included());
        });

        // Fetch the Registry and the Factory from the container
        $this->registry = $this->application->make(Registry::class);
        $this->jsonApiSpecFactory = $this->application->make(JsonApiSpecFactory::class);

        // Build the serializer
        $this->serializer = new Serializer($this->registry, $this->jsonApiSpecFactory);
    }

    public function testToJsonApiWithOneEntity(): void
    {
        $json = '{"jsonapi":{"version":"1.0"},"data":{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}}}';

        $user = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $this->setProtectedAttribute($user, 'id', 20);;

        $responseJson = $this->serializer->toJsonApi(
            $this->jsonApiSpecFactory->make($user)
        );

        $this->assertEquals($json, $responseJson);
    }

    public function testToJsonApiWithManyEntities(): void
    {
        $json = '{"jsonapi":{"version":"1.0"},"data":[{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}},{"type":"users","id":26,"attributes":{"name":"Josh Murray","email":"josh.murray@simpleonlinehealthcare.com","createdAt":"2023-06-14T19:42:08+00:00","updatedAt":"2023-06-19T10:32:13+00:00"}}]}';

        $userOne = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $userTwo = (new User())->setName('Josh Murray')
            ->setEmail('josh.murray@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-14T19:42:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-19T10:32:13+00:00'));

        $this->setProtectedAttribute($userOne, 'id', 20);
        $this->setProtectedAttribute($userTwo, 'id', 26);

        $responseJson = $this->serializer->toJsonApi(
            $this->jsonApiSpecFactory->make([$userOne, $userTwo])
        );

        $this->assertEquals($json, $responseJson);
    }

    public function testFromJsonApiWithOneEntity(): void
    {
        $userEntityClass = User::class;

        $json = '{"data":{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}}}';

        /** @var User $userFromJson */
        $userFromJson = $this->serializer->fromJsonApi($json, $userEntityClass);

        $this->assertInstanceOf($userEntityClass, $userFromJson);

        $user = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $this->setProtectedAttribute($user, 'id', 20);

        $this->assertEquals($user, $userFromJson);
    }

    public function testFromJsonApiWithManyEntities(): void
    {
        $userEntityClass = User::class;

        $json = '{"data":[{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}},{"type":"users","id":26,"attributes":{"name":"Josh Murray","email":"josh.murray@simpleonlinehealthcare.com","createdAt":"2023-06-14T19:42:08+00:00","updatedAt":"2023-06-19T10:32:13+00:00"}}]}';

        /** @var JsonApiSpec $result */
        $usersFromJson = $this->serializer->fromJsonApi($json, $userEntityClass);

        $this->assertIsArray($usersFromJson);

        $users = [
            (new User())->setName('Grant Owen')
                ->setEmail('john.doe@simpleonlinehealthcare.com')
                ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
                ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00')),
            (new User())->setName('Josh Murray')
                ->setEmail('josh.murray@simpleonlinehealthcare.com')
                ->setCreatedAt(Carbon::createFromTimeString('2023-06-14T19:42:08+00:00'))
                ->setUpdatedAt(Carbon::createFromTimeString('2023-06-19T10:32:13+00:00')),
        ];

        $this->setProtectedAttribute($users[0], 'id', 20);
        $this->setProtectedAttribute($users[1], 'id', 26);

        foreach ($usersFromJson as $key => $userFromJson) {
            $this->assertEquals($users[$key], $userFromJson);
        }
    }

    public function testToJsonApiWithOneEntityAndRelationship(): void
    {
        $expectedJson = '{"jsonapi":{"version":"1.0"},"data":{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"},"relationships":{"address":{"type":"addresses","id":1}}},"included":[{"type":"addresses","id":1,"attributes":{"lineOne":"Line One","lineTwo":"Line Two","postcode":"SP4 1GB","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}}]}';

        $user = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $address = (new Address())->setLineOne('Line One')
            ->setLineTwo('Line Two')
            ->setPostcode('SP4 1GB')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $this->setProtectedAttribute($user, 'id', 20);
        $this->setProtectedAttribute($address, 'id', 1);
        $user->setAddress($address);

        $responseJson = $this->serializer->toJsonApi(
            $this->jsonApiSpecFactory->make($user)
        );

        $this->assertEquals($expectedJson, $responseJson);
    }

    public function testToJsonApiWithManyEntitiesAndRelationships(): void
    {
        $expectedJson = '{"jsonapi":{"version":"1.0"},"data":[{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"},"relationships":{"address":{"type":"addresses","id":1}}},{"type":"users","id":21,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"},"relationships":{"address":{"type":"addresses","id":2}}}],"included":[{"type":"addresses","id":1,"attributes":{"lineOne":"Line One","lineTwo":"Line Two","postcode":"SP4 1GB","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}},{"type":"addresses","id":2,"attributes":{"lineOne":"Test Street","lineTwo":"Banana Lane","postcode":"GB1 1SL","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}}]}';

        $userOne = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $addressOne = (new Address())->setLineOne('Line One')
            ->setLineTwo('Line Two')
            ->setPostcode('SP4 1GB')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $userTwo = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $addressTwo = (new Address())->setLineOne('Test Street')
            ->setLineTwo('Banana Lane')
            ->setPostcode('GB1 1SL')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $this->setProtectedAttribute($userOne, 'id', 20);
        $this->setProtectedAttribute($userTwo, 'id', 21);
        $this->setProtectedAttribute($addressOne, 'id', 1);
        $this->setProtectedAttribute($addressTwo, 'id', 2);
        $userOne->setAddress($addressOne);
        $userTwo->setAddress($addressTwo);

        $responseJson = $this->serializer->toJsonApi(
            $this->jsonApiSpecFactory->make([$userOne, $userTwo])
        );

        $this->assertEquals($expectedJson, $responseJson);
    }
}