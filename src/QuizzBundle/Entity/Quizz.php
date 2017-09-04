<?php

namespace QuizzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quizz
 *
 * @ORM\Table(name="quizz")
 * @ORM\Entity(repositoryClass="QuizzBundle\Repository\QuizzRepository")
 */
class Quizz
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Theme", inversedBy="quizz")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
     */
    private $theme;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;


    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="quizz")
     */
    private $answer;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set question
     *
     * @param string $question
     *
     * @return Quizz
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->score = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add score
     *
     * @param \UserBundle\Entity\Score $score
     *
     * @return Quizz
     */
    public function addScore(\UserBundle\Entity\Score $score)
    {
        $this->score[] = $score;

        return $this;
    }

    /**
     * Remove score
     *
     * @param \UserBundle\Entity\Score $score
     */
    public function removeScore(\UserBundle\Entity\Score $score)
    {
        $this->score->removeElement($score);
    }

    /**
     * Get score
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set theme
     *
     * @param \QuizzBundle\Entity\Theme $theme
     *
     * @return Quizz
     */
    public function setTheme(\QuizzBundle\Entity\Theme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \QuizzBundle\Entity\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Add answer
     *
     * @param \QuizzBundle\Entity\Answer $answer
     *
     * @return Quizz
     */
    public function addAnswer(\QuizzBundle\Entity\Answer $answer)
    {
        $this->answer[] = $answer;

        return $this;
    }

    /**
     * Remove answer
     *
     * @param \QuizzBundle\Entity\Answer $answer
     */
    public function removeAnswer(\QuizzBundle\Entity\Answer $answer)
    {
        $this->answer->removeElement($answer);
    }

    /**
     * Get answer
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    public function __toString()
    {
        return $this->question;
    }
}
