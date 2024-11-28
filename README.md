Encrypted DB

Security process

Technology

• Symmetrical keys for folder data crypt
• Asymetrical keys for user, with high time cost passphrase decrypt
• Do not store private DB keys (decrypted) in memory, but encrypted with server passphrase to minimise risk with memory buffer overflow, etc.

Requirements

Have a minimum data compromision on compromised password, passphrase or DB key (the last case should not happen)
• If server comprimised, no (or minimum) data lost
• If user password compromised, only his data (his passwords, and passwords shared to him) can be retrieved by hacker even if he has access to the server data (whole database and scripts)

Process

• Each user has as robust password (passphrase), a couple of private and public Key (RSA) using the passphrase (to decrypt access to data encryption keys).
• Each shared folder has a private data encryption key (AES-256)
• Each user has initially his own private group, whose alone inside
• When a user is in a group, the group key is stored for the user encrypted with the user public key, this is done for each user inside the groupe, so that only a user in a group can decrypt data encrypted for the group, and permit a new user to decript data in the group, by decrypting the group key and encoding it with the new user public key.
• The server has a private passphrase or key (to define) to store securely temporary data in memory (for example, private folder keys)
• So, a folder private key is stored encrypted for each user with access
• The private folder key is user to encrypt and decrypt all data stored inside

Security failures use cases

User password stolen

Server data/db compromised

Appendix

Time to brute force

https://www.proxynova.com/tools/brute-force-calculator/

