import { Briefcase } from "lucide-react";

declare global {
    function route(name: string): string;
}

export const portfolioCompanyMenu = (t: (key: string) => string) => [
    {
        title: t("Portfolio"),
        icon: Briefcase,
        permission: "manage-portfolio",
        parent: "",
        order: 1025,
        children: [
            {
                title: t("Portfolios"),
                href: route("portfolio.portfolios.index"),
                permission: "manage-portfolios",
            },
            {
                title: t("Categories"),
                href: route("portfolio.categories.index"),
                permission: "manage-portfolio-categories",
            },
        ],
    },
];
