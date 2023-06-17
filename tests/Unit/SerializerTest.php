<?php

namespace Tests\Unit;

use Carbon\Carbon;
use SimpleOnlineHealthcare\JsonApi\Concerns\JsonApi;
use SimpleOnlineHealthcare\JsonApi\Factories\JsonApiSpecFactory;
use SimpleOnlineHealthcare\JsonApi\JsonApiSpec;
use SimpleOnlineHealthcare\JsonApi\Serializer;
use Tests\Concerns\Entities\User;
use Tests\TestCase;

class SerializerTest extends TestCase
{
    public function testToJsonApiWithOneEntity(): void
    {
        $serializer = $this->buildSerializer();

        $json = '{"jsonapi":{"version":"1.0"},"data":{"type":"users","id":20,"attributes":{"name":"Grant Owen","email":"john.doe@simpleonlinehealthcare.com","createdAt":"2023-06-13T15:02:08+00:00","updatedAt":"2023-06-13T15:02:08+00:00"}}}';

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

    protected function buildSerializer(): Serializer
    {
        return new Serializer(
            $this->application, new JsonApiSpecFactory(new JsonApi('1.0'))
        );
    }
}