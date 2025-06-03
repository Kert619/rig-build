import { z } from "zod";

export const loginSchema = z.object({
  email: z
    .string({ required_error: "Email is required" })
    .nonempty("Email is required")
    .email("Invalid email"),
  password: z
    .string({ required_error: "Password is required" })
    .nonempty({ message: "Password is required" }),
});

export type LoginSchema = z.output<typeof loginSchema>;
