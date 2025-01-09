<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="auth_user_networks", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"network_name", "network_identity"})
 * })
 */
final class UserNetwork
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private string $id;

    public function __construct(
        /**
         * @var User
         * @ORM\ManyToOne(targetEntity="User", inversedBy="networks")
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
         */
        private readonly User $user,
        /**
         * @ORM\Embedded(class="Network")
         */
        private readonly Network $network
    ) {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getNetwork(): Network
    {
        return $this->network;
    }
}
