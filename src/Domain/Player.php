<?php

namespace App\Domain;

class Player
{
    private ?int $id = null;
    private string $name;
    private string $gender;
    private int $skillLevel;
    private ?int $strength;
    private ?int $speed;
    private ?int $reactionTime;

    public function __construct(
        string $name = null, 
        int $skillLevel = 0, 
        string $gender = null, 
        ?int $strength = null, 
        ?int $speed = null, 
        ?int $reactionTime = null
    ) {
        if ($skillLevel < 0 || $skillLevel > 100) {
            throw new \InvalidArgumentException("skillLevel must be between 0 and 100");
        }

        if ($strength !== null && ($strength < 0 || $strength > 100)) {
            throw new \InvalidArgumentException("strength must be between 0 and 100");
        }

        if ($speed !== null && ($speed < 0 || $speed > 100)) {
            throw new \InvalidArgumentException("speed must be between 0 and 100");
        }

        if ($reactionTime !== null && ($reactionTime < 0 || $reactionTime > 100)) {
            throw new \InvalidArgumentException("reactionTime must be between 0 and 100");
        }

        if (!in_array($gender, ['M', 'F'])) {
            throw new \InvalidArgumentException("gender must be 'M' or 'F'");
        }

        if($gender == "F") {
            if(!$reactionTime) {
                throw new \InvalidArgumentException("reactionTime is required and must be between 0 and 100");
            }

            switch(true) {
                case $strength: throw new \InvalidArgumentException("Female does not support the attribute strength");
                case $speed: throw new \InvalidArgumentException("Female does not support the attribute speed");
            }
        }

        if($gender == "M") {
            if(!$strength) {
                throw new \InvalidArgumentException("strength is required and must be between 0 and 100");
            }
            if(!$speed) {
                throw new \InvalidArgumentException("speed is required and must be between 0 and 100");
            }


            switch(true) {
                case $reactionTime: throw new \InvalidArgumentException("Male does not support the attribute reactionTime");
            }
        }


        $this->name = $name;
        $this->skillLevel = $skillLevel;
        $this->gender = $gender;
        $this->strength = $strength;
        $this->speed = $speed;
        $this->reactionTime = $reactionTime;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getGender(): string { return $this->gender; }
    public function getSkillLevel(): int { return $this->skillLevel; }
    public function getStrength(): ?int { return $this->strength; }
    public function getSpeed(): ?int { return $this->speed; }
    public function getReactionTime(): ?int { return $this->reactionTime; }

   
    public function setId($id) {
        $this->id = $id;
    }

    public function __toArray(){
        return call_user_func('get_object_vars', $this);
    }
}
