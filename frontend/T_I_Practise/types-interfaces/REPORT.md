# Report - Task 003 - TYPES VS INTERFACES

In this task, I explored the similarities and differences between Type and Interface in TypeScript.
I practiced using extend, intersection, union, and interface merging.
I defined IUser as an interface (expandable, merge-friendly) and TAdminUser as a type (composable with utility types like Omit and flexible for optional fields).
The key difference was demonstrated through the department property:
it is required for IUser but optional for TAdminUser.
Additional examples showed how readonly, optional (?:), and | undefined behave differently.
