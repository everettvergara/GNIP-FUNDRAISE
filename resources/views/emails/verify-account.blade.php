<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your Good Neighbors Philippines account</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Figtree', Arial, Helvetica, sans-serif; color: #685b55;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px;">
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <img
                                src="{{ asset('images/design/logo-paper-plane.png') }}"
                                alt="Good Neighbors Philippines"
                                width="180"
                                style="display: block; max-width: 180px; height: auto;"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffffff; padding: 32px 28px; border: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 16px; font-size: 16px; line-height: 1.6; color: #685b55;">
                                Greeting {{ $user->first_name }} {{ $user->last_name }}!
                            </p>

                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.7; color: #685b55;">
                                Thank you for your interest in supporting Good Neighbor&rsquo;s Fundraising A. To complete your account creation and get your campaign ready to accept donations, please use the following link:
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 0 24px;">
                                <tr>
                                    <td align="center" style="border-radius: 9999px; background-color: #8aa330;">
                                        <a href="{{ $verificationUrl }}"
                                           style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 9999px;">
                                            Complete Your Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.7; color: #685b55;">
                                Please note that in order to have your campaign able to accept donations, the organization you requested to support will need to confirm your campaign after you are done creating it.
                            </p>

                            <p style="margin: 0 0 8px; font-size: 15px; line-height: 1.7; color: #685b55;">
                                Happy fundraising!
                            </p>

                            <p style="margin: 0; font-size: 15px; line-height: 1.7; color: #685b55; font-weight: 700;">
                                Good Neighbors
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
