---
name: security
description: Use when implementing authentication, authorization, policies, gates, or when security concerns are relevant. Provides Laravel security patterns.
---

# Security Skill

Security patterns for Laravel applications.

## Authorization

### Policies
```php
// Create policy
php artisan make:policy TournamentPolicy --model=Tournament

// In policy
public function update(User $user, Tournament $tournament): bool
{
    return $user->id === $tournament->user_id;
}

public function delete(User $user, Tournament $tournament): bool
{
    return $user->id === $tournament->user_id;
}
```

### Using Policies

```php
// In controller
public function update(Tournament $tournament)
{
    $this->authorize('update', $tournament);
    // ...
}

// In Blade
@can('update', $tournament)
    <button>Edit</button>
@endcan

// In Livewire
public function delete(Tournament $tournament)
{
    $this->authorize('delete', $tournament);
    $tournament->delete();
}
```

### Gates
```php
// In AppServiceProvider or AuthServiceProvider
Gate::define('manage-tournaments', function (User $user) {
    return $user->is_admin;
});

// Usage
if (Gate::allows('manage-tournaments')) {
    // ...
}
```

## Validation Security

### Mass Assignment Protection
```php
// In Model - whitelist approach (preferred)
protected $fillable = ['name', 'date', 'format'];

// Or blacklist approach
protected $guarded = ['id', 'created_at', 'updated_at'];
```

### Form Requests
```php
class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or custom logic
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after:today'],
            'format' => ['required', Rule::enum(TournamentFormat::class)],
        ];
    }
}
```

## Common Vulnerabilities to Avoid

### SQL Injection
```php
// BAD - vulnerable
DB::select("SELECT * FROM users WHERE name = '$name'");

// GOOD - parameterized
DB::select('SELECT * FROM users WHERE name = ?', [$name]);

// BEST - use Eloquent
User::where('name', $name)->get();
```

### XSS (Cross-Site Scripting)
```blade
{{-- BAD - unescaped --}}
{!! $userInput !!}

{{-- GOOD - escaped by default --}}
{{ $userInput }}
```

### CSRF Protection
```blade
{{-- Always include in forms --}}
<form method="POST">
    @csrf
    ...
</form>
```

## Exception Handling

### Custom Exceptions
```php
class TournamentFullException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Tournament is full'], 422);
        }

        return back()->with('error', 'Tournament is full');
    }
}
```

### Logging Sensitive Data
```php
// BAD - logging sensitive data
Log::info('User login', ['password' => $password]);

// GOOD - redact sensitive fields
Log::info('User login', ['user_id' => $user->id]);
```

## Checklist

- [ ] All user input validated via Form Requests
- [ ] Authorization checks on all destructive actions
- [ ] No raw SQL with user input
- [ ] CSRF tokens on all forms
- [ ] Sensitive data not logged
- [ ] Mass assignment protection configured
- [ ] No secrets in version control
