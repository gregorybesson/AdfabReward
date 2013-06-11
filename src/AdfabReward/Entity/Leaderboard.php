<?php
namespace AdfabReward\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="reward_leaderboard",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uniq_user_leaderboard_type", columns={"user_id", "leaderboardtype_id"})},
 *     indexes={@ORM\Index(name="idx_week_points", columns={"week_points"}),@ORM\Index(name="idx_total_points", columns={"total_points"})})
 */
class Leaderboard
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AdfabUser\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AdfabReward\Entity\LeaderboardType")
     * @ORM\JoinColumn(name="leaderboardtype_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $leaderboardType;

    /**
     * Le nombre de points totaux 
     * @ORM\Column(name="total_points",type="integer",columnDefinition="MEDIUMINT UNSIGNED NOT NULL DEFAULT 0")
     */
    protected $totalPoints;

    /**
     * Le nombre de points sur la semaine courante 
     * @ORM\Column(name="week_points",type="integer",columnDefinition="MEDIUMINT UNSIGNED NOT NULL DEFAULT 0")
     */
    protected $weekPoints;
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /** @PrePersist */
    public function createChrono()
    {
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @param $id
     * @return Block|mixed
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param field_type $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return the $leaderboardType
     */
    public function getLeaderboardType()
    {
        return $this->leaderboardType;
    }

    /**
     * @param field_type $leaderboardType
     */
    public function setLeaderboardType($leaderboardType)
    {
        $this->leaderboardType = $leaderboardType;
    }

    /**
     * @return the $totalPoints
     */
    public function getTotalPoints()
    {
        return $this->totalPoints;
    }

    /**
     * @param field_type $totalPoints
     */
    public function setTotalPoints($totalPoints)
    {
        $this->totalPoints = $totalPoints;
    }

    /**
     * @return the $weekPoints
     */
    public function getWeekPoints()
    {
        return $this->weekPoints;
    }

    /**
     * @param field_type $weekPoints
     */
    public function setWeekPoints($weekPoints)
    {
        $this->weekPoints = $weekPoints;
    }
    
    /**
     *
     * @return the $createdAt
     */
    public function getCreatedAt ()
    {
        return $this->createdAt;
    }

    /**
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt ($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return the $updatedAt
     */
    public function getUpdatedAt ()
    {
        return $this->updatedAt;
    }

    /**
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt ($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
