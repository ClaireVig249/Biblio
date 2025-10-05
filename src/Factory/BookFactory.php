<?php

namespace App\Factory;

use App\Entity\Book;
use App\Enum\BookStatus;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Book>
 */
final class BookFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Book::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [

            'title' => self::faker()->unique()->sentence(),
            'isbn' => self::faker()->isbn13(),
            'cover' => self::faker()->imageUrl(330, 500, 'couverture', true),
            'editedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'plot' => self::faker()->paragraphs(3, true),
            'pageNumber' => self::faker()->numberBetween(50, 1000), // ou randomNumber
            'status' => self::faker()->randomElement(BookStatus::cases()),
            'authors' => AuthorFactory::random(), // ou AuthorFactory::new()->many(self::faker()->numberBetween(1, 3)),
            'editor' => EditorFactory::random(),
            'createdBy' => (new UserFactory)->getTestUser('ROLE_USER'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Book $book): void {})
        ;
    }
}
