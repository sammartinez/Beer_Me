import nodemailer from "nodemailer";

// Without SMTP_HOST configured, emails are rendered to JSON and logged
// instead of sent, so the send-a-token flow works in development.
const transporter = process.env.SMTP_HOST
  ? nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: Number(process.env.SMTP_PORT ?? 587),
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
      },
    })
  : nodemailer.createTransport({ jsonTransport: true });

export async function sendTokenNotification(to: string, name: string, patronId: number) {
  const appUrl = process.env.APP_URL ?? "http://localhost:8000";
  const info = await transporter.sendMail({
    from: process.env.MAIL_FROM ?? "Beer Me! <beerme@localhost>",
    to: `"${name}" <${to}>`,
    subject: "Somebody sent you a token!",
    html: `<a href='${appUrl}/confirmation/${patronId}'>Click here to view your token.</a>`,
    text: "You received a token! Log in to your account to view your token.",
  });
  if (!process.env.SMTP_HOST) {
    // With jsonTransport the rendered message comes back on info.message.
    console.log("SMTP not configured; email not sent:", (info as { message?: string }).message);
  }
}
