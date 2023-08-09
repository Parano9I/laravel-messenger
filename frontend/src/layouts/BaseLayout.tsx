import {FC, ReactNode} from "react";
import {Outlet} from "react-router-dom";

interface MainLayoutProps {
    children?: ReactNode
}

const BaseLayout: FC<MainLayoutProps> = ({children}) => {
    return <div className="bg-orange-700">{children || <Outlet/>}</div>
}

export default BaseLayout;