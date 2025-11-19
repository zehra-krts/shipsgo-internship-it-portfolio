//Reusable union as a type
type TDepartment = "engineering" | "design" | "marketing";

//INTERFACE VERSION:
interface IUser {
    readonly id: number;
    readonly name: string;
    email: string;
    phone?: string;
    department: TDepartment;
}

// Interface makes it possible to redefine with the same name
interface IUser {
    salary?: number;
    dayoff?: number;
}
interface IAdminUser extends IUser {
    canAssign: boolean;
    accessLevel: "high" | "medium" | "low";
}

// TYPE VERSION:
type TUser = {
    readonly id: number;
    readonly name: string;
    email: string;
    phone?: string;
    department: TDepartment;
    salary?: number;
    dayoff?: number;
};
//Omit > It is used to remove certain fields from a type.
type TAdminUser = Omit<IUser, "department"> & {
    canAssign: boolean;
    accessLevel: "high" | "medium" | "low";
};

// Partial makes all of these fields optional.
type TUserUpdate = Partial<TUser>;

// Pick > It retrieves only the id and name fields from IUser.
type TUserIdentity = Pick<IUser, "id" | "name">;

// Interface examples:
const user1: IUser = {
    id: 1,
    name: "Ece",
    email: "ece@example.com",
    department: "engineering",
    salary: 90000,
    dayoff: 2,
};

const admin1: IAdminUser = {
    id: 2,
    name: "Göksu",
    email: "goksu@example.com",
    department: "design",
    canAssign: true,
    accessLevel: "high",
};

//Type examples:
const user2: TUser = {
    id: 3,
    name: "Zeynep",
    email: "zeynep@example.com",
    department: "marketing",
};

const admin2: TAdminUser = {
    id: 4,
    name: "Sude",
    email: "sude@example.com",
    canAssign: true,
    accessLevel: "medium",
    // department > omit
};

// Partial tes:
const userDemo: TUserUpdate = {
    email: "name@example.com",
    dayoff: 5,
    // everything optional
};

// Pick test:
const userDemo2: TUserIdentity = {
    id: 5,
    name: "Eda",
};

// Readonly test:
// user1.id = 999; (Error: 'id' is readonly)

console.log("[IUser]", user1);
console.log("[IAdminUser]", admin1);
console.log("[TUser]", user2);
console.log("[TAdminUser]", admin2);
console.log("[Partial<TUser>]", userDemo);
console.log("[Pick<IUser>]", userDemo2);
