<?php
namespace AdfabReward\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reward_action")
 */
class Action {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", unique=true);
	 */
	protected $id;

	/**
	 * Le sujet à l'origine de l'action (le module responsable)
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $subject;

	/**
	 * L'action à accomplir
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $verb;

	/**
	 * Ce sur quoi l'action est réalisée
	 * @ORM\Column(type="string", nullable=true)
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
	public function createChrono() {
		$this->created_at = new \DateTime("now");
		$this->updated_at = new \DateTime("now");
	}

	/** @PreUpdate */
	public function updateChrono() {
		$this->updated_at = new \DateTime("now");
	}

	/**
	 * @param  string $property
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param  string $property
	 * @return mixed
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param  string $property
	 * @return mixed
	 */
	public function getVerb() {
		return $this->verb;
	}

	/**
	 * @param  string $property
	 * @return mixed
	 */
	public function getComplement() {
		return $this->complement;
	}

	/**
	 * @param  string $property
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	public function getPoints() {
		return $this->points;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}

	public function setVerb($verb) {
		$this->verb = $verb;
		return $this;
	}

	public function setComplement($complement) {
		$this->complement = $complement;
		return $this;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function setPoints($points) {
		$this->points = $points;
		return $this;
	}

	public function getRateLimit() {
		return $this->rate_limit;
	}

	public function setRateLimit($rate_limit) {
		$this->rate_limit = $rate_limit;
		return $this;
	}

	public function getRateLimitDuration() {
		return $this->rate_limit_duration;
	}

	public function setRateLimitDuration($rate_limit_duration) {
		$this->rate_limit_duration = $rate_limit_duration;
		return $this;
	}

	public function getCountLimit() {
		return $this->count_limit;
	}

	public function setCountLimit($count_limit) {
		$this->count_limit = $count_limit;
		return $this;
	}

	public function getTeamCredit() {
		return $this->team_credit;
	}

	public function setTeamCredit($team_credit) {
		$this->team_credit = $team_credit;
		return $this;
	}

	public function getLeaderboardTypes() {
		return $this->leaderboard_types;
	}

	public function setLeaderboardTypes($leaderboard_types) {
		$this->leaderboard_types = $leaderboard_types;
		return $this;
	}
	
	/**
	 * Add a LeaderboardType to the action.
	 *
	 * @param Role $role
	 *
	 * @return void
	 */
	public function addLeaderboardType($leaderboard_type)
	{
		$this->leaderboard_types[] = $leaderboard_type;
	}

	public function getCreatedAt() {
		return $this->created_at;
	}

	public function setCreatedAt($created_at) {
		$this->created_at = $created_at;
		return $this;
	}

	public function getUpdatedAt() {
		return $this->updated_at;
	}

	public function setUpdatedAt($updated_at) {
		$this->updated_at = $updated_at;
		return $this;
	}

	/**
	 * Convert the object to an array.
	 *
	 * @return array
	 */
	public function getArrayCopy() {
		return get_object_vars($this);
	}

	/**
	 * Populate from an array.
	 *
	 * @param array $data
	 */
	public function populate($data = array()) {
		$this->id = $data['id'];
		/*$this->username = $data['username'];
			$this->email = $data['email'];
		$this->displayName = $data['displayName'];
		$this->password = $data['password'];
		$this->state = $data['state'];*/
	}
}
