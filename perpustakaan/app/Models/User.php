<?php
// app/Models/User.php — interface + abstract class User (OOP core)

namespace App\Models;

// ── Interface ──────────────────────────────────────────────────────────────
interface Authenticatable
{
    public function login(string $email, string $password): bool;
    public function logout(): void;
    public function isLoggedIn(): bool;
}

interface Manageable
{
    public function getAll(): array;
    public function findById(string $id): ?array;
    public function save(array $data): bool;
    public function delete(string $id): bool;
}

// ── Abstract Class ─────────────────────────────────────────────────────────
abstract class User implements Authenticatable
{
    // Encapsulation: properti private/protected
    protected string $id;
    protected string $nama;
    protected string $email;
    private   string $password;
    protected string $role;
    protected string $status;
    protected string $telp;
    protected string $alamat;
    protected string $foto;
    protected string $created;

    // Constructor wajib di setiap class
    public function __construct(array $data = [])
    {
        $this->id       = $data['id']      ?? '';
        $this->nama     = $data['nama']    ?? '';
        $this->email    = $data['email']   ?? '';
        $this->password = $data['password'] ?? '';
        $this->role     = $data['role']    ?? '';
        $this->status   = $data['status']  ?? 'aktif';
        $this->telp     = $data['telp']    ?? '';
        $this->alamat   = $data['alamat']  ?? '';
        $this->foto     = $data['foto']    ?? '';
        $this->created  = $data['created'] ?? date('Y-m-d');
    }

    // ── Getters (Encapsulation) ──────────────────────────────────────────
    public function getId(): string    { return $this->id; }
    public function getNama(): string  { return $this->nama; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string  { return $this->role; }
    public function getStatus(): string{ return $this->status; }
    public function getTelp(): string  { return $this->telp; }
    public function getAlamat(): string{ return $this->alamat; }
    public function getFoto(): string  { return $this->foto; }

    // Password getter hanya untuk kebutuhan internal verify
    protected function getPassword(): string { return $this->password; }

    // ── Static method: generate ID ───────────────────────────────────────
    public static function generateId(string $prefix, array $existing): string
    {
        $max = 0;
        foreach ($existing as $key => $_) {
            $num = (int) substr($key, strlen($prefix));
            if ($num > $max) $max = $num;
        }
        return $prefix . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
    }

    // ── Authenticatable implementation ──────────────────────────────────
    public function login(string $email, string $password): bool
    {
        $users = $_SESSION['users'] ?? [];
        foreach ($users as $u) {
            if ($u['email'] === $email && password_verify($password, $u['password'])) {
                if ($u['status'] !== 'aktif') return false;
                $_SESSION['auth'] = [
                    'id'    => $u['id'],
                    'nama'  => $u['nama'],
                    'email' => $u['email'],
                    'role'  => $u['role'],
                    'foto'  => $u['foto'],
                ];
                return true;
            }
        }
        return false;
    }

    public function logout(): void
    {
        unset($_SESSION['auth']);
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['auth']);
    }

    // ── Abstract method — di-override tiap subclass (Polymorphism) ──────
    abstract public function getDashboardData(): array;
    abstract public function getMenuItems(): array;

    // ── Konversi ke array ────────────────────────────────────────────────
    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'nama'     => $this->nama,
            'email'    => $this->email,
            'password' => $this->password,
            'role'     => $this->role,
            'status'   => $this->status,
            'telp'     => $this->telp,
            'alamat'   => $this->alamat,
            'foto'     => $this->foto,
            'created'  => $this->created,
        ];
    }
}
