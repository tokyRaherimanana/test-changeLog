<?php
// src/Entity/Changelog.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'changelog')]
class Changelog
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $version = null;

    #[ORM\Column(type: 'string', length: 40)]
    private string $commitHash;

    #[ORM\Column(type: 'string', length: 20)]
    private string $type = 'other';

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $trelloCardId = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $trelloCardShortlink = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $trelloCardUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $trelloCardName = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $files = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $committedAt = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // === getters / setters ===
    public function getId(): ?int { return $this->id; }
    public function getVersion(): ?string { return $this->version; }
    public function setVersion(?string $v): self { $this->version = $v; return $this; }
    public function getCommitHash(): string { return $this->commitHash; }
    public function setCommitHash(string $h): self { $this->commitHash = $h; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): self { $this->type = $t; return $this; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $d): self { $this->description = $d; return $this; }
    public function getTrelloCardId(): ?string { return $this->trelloCardId; }
    public function setTrelloCardId(?string $v): self { $this->trelloCardId = $v; return $this; }
    public function getTrelloCardShortlink(): ?string { return $this->trelloCardShortlink; }
    public function setTrelloCardShortlink(?string $v): self { $this->trelloCardShortlink = $v; return $this; }
    public function getTrelloCardUrl(): ?string { return $this->trelloCardUrl; }
    public function setTrelloCardUrl(?string $v): self { $this->trelloCardUrl = $v; return $this; }
    public function getTrelloCardName(): ?string { return $this->trelloCardName; }
    public function setTrelloCardName(?string $v): self { $this->trelloCardName = $v; return $this; }
    public function getFiles(): ?array { return $this->files; }
    public function setFiles(?array $f): self { $this->files = $f; return $this; }
    public function getAuthor(): ?string { return $this->author; }
    public function setAuthor(?string $a): self { $this->author = $a; return $this; }
    public function getCommittedAt(): ?\DateTimeInterface { return $this->committedAt; }
    public function setCommittedAt(?\DateTimeInterface $d): self { $this->committedAt = $d; return $this; }
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
