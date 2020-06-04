<?php
/**
 * UserLog.php
 *
 * UserLog Entity
 *
 * @category   Entity
 * @package    JWTapp
 * @author     Santos Agudo
 * @copyright  2020 www.santosagudo,.es
 * @license    http://www.santosagudo.es/license/3_0.txt  PHP License 3.0
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * UserLog
 *
 * @ORM\Table(name="user_log");
 * @ORM\Entity(repositoryClass="App\Repository\UserLogRepository");
 * @ORM\HasLifecycleCallbacks()
 */
class UserLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
