# ActiveCollab-RocketChat
Based on [activecollab-slack](https://github.com/bartram/activecollab-slack).
RocketChat integration for Active Collab 4. Following events are pushed to a pre-defined channel:
  * new task
  * new comments
  * new subtask
  * completed subtask
  * completed tasks

## Requirements
  * Active Collab 4
  * custom field 1 is not yet in use for projects

## Installation
1. Download latest release and extract them to `custom/modules` within your Active Collab.
2. Create a RocketChat API keypair:
   * Login user
   * Profile -> My Account -> Personal Access Tokens
   * Create a Personal Token 
3. Open your `config/config.php` and add url, token and userid:
   * `define('ROCKETCHAT_TOKEN',  '1234567890qwertyuiopasdfghjklzxcvbnm1234567');`
   * `define('ROCKETCHAT_USERID', 'ABCDEFGHIJKLMN321');`
   * `define('ROCKETCHAT_URL',    'https://rocketchat.example.com');`
4. Login as admin and install module via "Administration → Modules".
5. Go to "Administration → Project Settings" and enable the first custom field.

## Configuration
The custom field will now appear in project settings. Enter your channel name here.

## License
Open source licensed under the MIT license (see LICENSE file for details).

## Donate
BTC 1DcMtyqNATFr5zqncugWrm9ArLFh7yFozQ
