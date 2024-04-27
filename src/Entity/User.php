<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Security\Core\User\UserInterface;
//groups = {"registration", "update_profile"  , "login"}
/**
 * User
 *
 * @ORM\Table(name="utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="Email", columns={"Email"})})

 * @ORM\Entity
 */
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface , PasswordAuthenticatedUserInterface
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
     * @ORM\Column(name="Email", type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     * message = "The email cannot be blank.",
     *  groups = {"registration"  , "login"}
     * )
     * @Assert\Email(
     * message = "The email '{{ value }}' is not a valid email.",
     * groups = {"registration"  , "login"}
     * )
     * 
     * 
     
     * 
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="Password", type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     * message = "The password cannot be blank.",
     * groups = {"registration"  , "login"}
     * )
     * @Assert\Length(
     * min = 6,
     * minMessage = "Your password must be at least {{ limit }} characters long",
     * groups = {"registration"  , "login"}
     * )
     * @Assert\Regex(
     * pattern="/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/",
     * message="Your password must contain at least one letter, one number and one special character",
     * groups = {"registration"  , "login"}
     * )
     * 
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *  message = "The name cannot be blank.",
     *   groups = {"registration", "update_profile"  }
     * 
     * )
     * @Assert\Length(
     * min = 3,
     * minMessage = "Your name must be at least {{ limit }} characters long",
     * max = 25,
     * maxMessage = "Your name cannot be longer than {{ limit }} characters",
     * groups = { "update_profile", "registration"}
     * )
     * @Assert\Regex(
     * pattern="/^[a-zA-Zéèàùïöüëïÿâêîôûëïüÿæœç]+$/",
     * message="Your name must contain only letters",
     * groups = { "update_profile", "registration"}
     * )
     * 
     * 
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Age", type="integer", nullable=true, options={"default"="NULL"})
     * @Assert\NotBlank(
     * message = "The age cannot be blank.",
     *  groups = { "update_profile" }
     * )
     * @Assert\Range(
     *    min = 18,
     *   max = 100,
     * notInRangeMessage = "The age must be between {{ min }} and {{ max }}",
     * groups = { "update_profile" }
     * )
     * 
    
     */
    private $age = NULL;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Phone", type="integer", nullable=true, options={"default"="NULL"})
     * @Assert\NotBlank(
     * message = "The phone cannot be blank.",
     *   groups = { "update_profile" }
     * )
     * @Assert\Range(
     *     min = 10000000,
     *    max = 99999999,
     *   notInRangeMessage = "The phone number must be between {{ min }} and {{ max }}",
     *  groups = { "update_profile" }
     * )
    
     * 
     * 
     */
    private $phone = NULL;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Address", type="string", length=255, nullable=true, options={"default"="NULL"})
     * @Assert\NotBlank(
     *  message = "The address cannot be blank.",
     *   groups = { "update_profile"}
     * )
   
     * 
     */
    private $address = NULL;

    /**
     * @var string
     *
     * @ORM\Column(name="Role", type="string", length=255, nullable=false , options={"default"="NULL"})
     */
    private $role;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Image", type="string", length=255, nullable=true, options={"NULL"})
     */
    private $image = NULL;

    /**
     * @var bool
     *
     * @ORM\Column(name="Status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="DatedeCreation", type="string", length=255, nullable=false)
     */
    private $datedecreation;

    /**
     * @var string
     *
     * @ORM\Column(name="VerificationCode", type="string", length=255, nullable=false)
     */
    private $verificationcode;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_Verified", type="boolean", nullable=false)
     */
    
    private $isVerified = false;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private $googleId;


    // construct 
    public function __construct()
    {
        $this->datedecreation = (new DateTimeImmutable())->format('Y-m-d');
        $this->verificationcode = $this->generateVerificationCode();
        $this->status = true; 
    }

    private function generateVerificationCode(): string
    {
        $length = 6; // Length of the verification code
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = '';
        for ($i = 0; $i < $length; $i++) {
            $verificationCode .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $verificationCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
    }

    

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDatedecreation(): ?string
    {
        return $this->datedecreation;
    }

    public function setDatedecreation(string $datedecreation): static
    {
        $this->datedecreation = $datedecreation;

        return $this;
    }

    public function getVerificationcode(): ?string
    {
        return $this->verificationcode;
    }

    public function setVerificationcode(string $verificationcode): static
    {
        $this->verificationcode = $verificationcode;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

    private $roles;

  

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

   

    public function getRoles(): array
    {
         return [$this->role];
        // guarantee every user at least has ROLE_USER
       
    }
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt() 
    {
        // Leave empty unless you are using bcrypt or another hashing method that requires a salt
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

   

}
