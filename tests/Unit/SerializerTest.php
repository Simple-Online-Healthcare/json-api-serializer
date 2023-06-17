<?php

namespace Tests\Unit;

use Carbon\Carbon;
use SimpleOnlineHealthcare\JsonApi\Concerns\Included;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;
use SimpleOnlineHealthcare\JsonApi\Serializer;
use Tests\Concerns\Entities\Address;
use Tests\Concerns\Entities\User;
use Tests\TestCase;

class SerializerTest extends TestCase
{
    public function testToJsonApiWithOneEntity(): void
    {
        $serializer = $this->buildSerializer();

        $json = '{"jsonapi":{"version":"1.0"},"data":{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"},"relationships":{"address":[]}},"included":[]}';

        $user = (new User())->setName('Grant Owen')
            ->setEmail('john.doe@simpleonlinehealthcare.com')
            ->setCreatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'))
            ->setUpdatedAt(Carbon::createFromTimeString('2023-06-13T15:02:08+00:00'));

        $this->setProtectedAttribute($user, 'id', 20);

        $responseJson = $serializer->toJsonApi($user);

        $this->assertEquals($json, $responseJson);
    }

    public function testToJsonApiWithManyEntities(): void
    {
        $serializer = $this->buildSerializer();

        $json = '{"jsonapi":{"version":"1.0"},"data":[{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"},"relationships":{"address":[]}},{"type":"users","id":26,"attributes":{"name":"Josh Murray","email":"josh.murray@simpleonlinehealthcare.com","createdAt":"2023-06-14T19:42:08+00:00","updatedAt":"2023-06-19T10:32:13+00:00"},"relationships":{"address":[]}}],"included":[]}';

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

        $responseJson = $serializer->toJsonApi([$userOne, $userTwo]);

        $this->assertEquals($json, $responseJson);
    }

    public function testFromJsonApiWithOneEntity(): void
    {
        $serializer = $this->buildSerializer();
        $userEntityClass = User::class;

        $json = '{"data":{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}}}';

        /** @var User $userFromJson */
        $userFromJson = $serializer->fromJsonApi($json, $userEntityClass);

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
        $serializer = $this->buildSerializer();
        $userEntityClass = User::class;

        $json = '{"data":[{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}},{"type":"users","id":26,"attributes":{"name":"Josh Murray","email":"josh.murray@simpleonlinehealthcare.com","createdAt":"2023-06-14T19:42:08+00:00","updatedAt":"2023-06-19T10:32:13+00:00"}}]}';

        /** @var JsonApiSpec $result */
        $usersFromJson = $serializer->fromJsonApi($json, $userEntityClass);

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
        $serializer = $this->buildSerializer();

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

        $responseJson = $serializer->toJsonApi($user);

        $this->assertEquals($expectedJson, $responseJson);
    }

    public function testToJsonApiWithManyEntitiesAndRelationships(): void
    {
        $serializer = $this->buildSerializer();

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

        $responseJson = $serializer->toJsonApi([$userOne, $userTwo]);

        $this->assertEquals($expectedJson, $responseJson);
    }

    protected function buildSerializer(): Serializer
    {
        return new Serializer(
            $this->application, new JsonApiSpecFactory(new JsonApi('1.0'), new Included())
        );
    }
}