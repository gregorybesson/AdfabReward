<?php
namespace AdfabReward\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reward_action")
 */
class Action
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Le sujet à l'origine de l'action (le module responsable)
     * @ORM\Column(type="string")
     */
    protected $subject;

    /**
     * L'action à accomplir
     * @ORM\Column(type="string")
     */
    protected $verb;

    /**
     * Ce sur quoi l'action est réalisée
     * @ORM\Column(type="string")
     */
    protected $complement;

    /**
     * Le nom de l'action
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * Le nombre de points que rapporte cette action
     * @ORM\Column(type="integer")
     */
    protected $points;

    /**
     * Limitation à n action dans le temps. Si valeur = 0, alors pas de limote
     * @ORM\Column(type="integer")
     */
    protected $rate_limit;

    /**
     * Durée de la limitation
     * @ORM\Column(type="integer")
     */
    protected $rate_limit_duration;

    /**
     * Limitation à n actions en tout
     * @ORM\Column(type="integer")
     */
    protected $count_limit;

    /**
     * Booléen déterminant si l'équipe du joueur est créditée
     * @ORM\Column(type="boolean")
     */
    protected $team_credit;
    
    /**
    * @ORM\ManyToMany(targetEntity="AdfabReward\Entity\LeaderboardType", inversedBy="actions")
    * @ORM\JoinTable(name="reward_action_leaderboard_type")
    */
    protected $leaderboard_types;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated_at;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->leaderboard_types = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /** @PrePersist */
    public function createChrono()
    {
        $this->created_at = new \DateTime("now");
        $this->updated_at = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * @param  string $property
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $property
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param  string $property
     * @return mixed
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param  string $property
     * @return mixed
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * @param  string $property
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function getRateLimit()
    {
        return $this->rate_limit;
    }

    public function getRateLimitDuration()
    {
        return $this->rate_limit_duration;
    }

    public function getCountLimit()
    {
        return $this->count_limit;
    }

    public function getTeamCredit()
    {
        return $this->team_credit;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
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

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        $this->id = $data['id'];
        /*$this->username = $data['username'];
        $this->email = $data['email'];
        $this->displayName = $data['displayName'];
        $this->password = $data['password'];
        $this->state = $data['state'];*/
    }
}
