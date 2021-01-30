# Laravel Invites

Invite users to your application

## Installation

You can install this package via composer

```
composer require jhacobs/laravel-invites
```

Publish the invites config file

```
php artisan vendor:publish --provider="Invites\InviteServiceProvider" --tag=config
```

Publish the migration

```
php artisan vendor:publish --provider="Invites\InviteServiceProvider" --tag=migrations
```

Now run `php artisan migrate` to create the invites table.

## Usage

### Prepare your model

Add the CanBeInvited interface to your user model

```php
use Jhacobs\Invites\Contracts\CanBeInvited;

class User extends Authenticatable implements CanBeInvited
```

Implement the getEmailForInvites method, so the package knows which field is has the user's email.

```php
public function sendInviteNotification(string $token)
{
    return 'email';
}
```

Implement the sendInviteNotification to send the invite notification to a user.

```php
public function sendInviteNotification(string $token)
{
    $this->notify(new InviteUserNotification($token));
}
```

### Send invite notification

To send the invite notification, call the sendInviteLink method on the Invite facade.

```php
use Jhacobs\Invites\Facades\Invite;
use App\Models\User;

$user = User::find(1);

Invite::sendInviteLink($user);
```

### Create a user's password

To create a user's password, call the createPassword method on the Invite facade. 

You must pass the user's email, the unhashed token created by the sendInviteLink method and the new password for the user.

```php
use Jhacobs\Invites\Facades\Invite;

Invite::createPassword($request->get('email'), $request->get('token'), $request->get('password'), function (User $user, string $password) {
    $user->password = Hash::make($password);
    $user->save();
});
```

## License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
