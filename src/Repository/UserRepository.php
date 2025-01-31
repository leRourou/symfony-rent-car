<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, Searchable
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */

    public function search(
        $limit = 10,
        $page = 1,
        $searchTerm = null
    ) {
        $qb = $this->createQueryBuilder('u');

        if ($searchTerm) {
            $qb->where('u.firstname LIKE :searchTerm')
                ->orWhere('u.lastname LIKE :searchTerm')
                ->orWhere('u.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $offset = ($page - 1) * $limit;

        $data = $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $count = 0;
        if (count($data) > 0) {
            $count = $qb->select('COUNT(u.id)')
                ->setFirstResult(null)
                ->setMaxResults(null)
                ->getQuery()
                ->getSingleScalarResult();
        }

        return [
            'data' => $data,
            'count' => $count
        ];
    }


    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //Delete user
    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
