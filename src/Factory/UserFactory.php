<?php

namespace App\Factory;

use AllowDynamicProperties;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
#[AllowDynamicProperties]
final class UserFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;

    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'username' => 'test',
            'firstname' => self::faker()->text(),
            'lastname' => self::faker()->text(),
            'email' => 'test@yopmail.com',
            'roles' => ['ROLE_USER'],
            'password' => $this->hasher->hashPassword(new User(), 'test'),
            'lastLoginAt' => new \DateTimeImmutable(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }

    /**
     * @param String|null $role
     * @return User
     */
    public function getTestUser(?String $role): User
    {
        return new User(
            'test',
            'test',
            'test',
            'test@yopmail.com',
            $role ? [$role] : ['ROLE_USER'],
            'test',
            new \DateTimeImmutable()
        );
    }
}
