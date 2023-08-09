import type {RouteObject} from 'react-router';
import {lazy} from "react";

const BaseLayout = lazy(() => import('@/layouts/BaseLayout.tsx'));

const HomePage = lazy(() => import('@/pages/Home.tsx'));
const RegisterPage = lazy(() => import('@/pages/auth/Register.tsx'));
const LoginPage = lazy(() => import('@/pages/auth/Login.tsx'));

const routes: RouteObject[] = [
    {
        path: '/',
        element: (<BaseLayout><HomePage/></BaseLayout>)
    },
    {
        path: 'auth',
        element: (<BaseLayout/>),
        children: [
            {
                index: true,
                path: 'register',
                element: (<RegisterPage/>)
            },
            {
                index: true,
                path: 'login',
                element: (<LoginPage/>)
            }
        ]
    }
]

export default routes;