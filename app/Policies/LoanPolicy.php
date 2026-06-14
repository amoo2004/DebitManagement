<?php
namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function view(User $user, Loan $loan): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Loan $loan): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function delete(User $user, Loan $loan): bool
    {
        return $user->role === 'admin';
    }
}
