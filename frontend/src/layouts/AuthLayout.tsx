import {FC, ReactNode} from "react";
import {Outlet} from "react-router-dom";

interface AuthLayoutProps {
    children?: ReactNode;
}

const AuthLayout: FC<AuthLayoutProps> = ({children}) => {
    return (<div>{children || <Outlet/>}</div>)
}

export default AuthLayout;