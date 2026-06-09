import { DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import InputError from "@/components/ui/input-error";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { EditPortfolioCategoryProps, EditPortfolioCategoryFormData } from "./types";
import { usePage } from "@inertiajs/react";

export default function EditPortfolioCategory({
    portfoliocategory,
    onSuccess,
}: EditPortfolioCategoryProps) {
    const { } = usePage<any>().props;
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } =
        useForm<EditPortfolioCategoryFormData>({
            name: portfoliocategory.name ?? "",
            description: portfoliocategory.description ?? "",
            is_active: portfoliocategory.is_active ?? false,
        });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route("portfolio.categories.update", portfoliocategory.id), {
            onSuccess: () => {
                onSuccess();
            },
        });
    };

    return (
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{t("Edit Category")}</DialogTitle>
            </DialogHeader>
            <form onSubmit={submit} className="space-y-4">
                <div>
                    <Label htmlFor="name">{t("Name")}</Label>
                    <Input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData("name", e.target.value)}
                        placeholder={t("Enter Name")}
                        required
                    />
                    <InputError message={errors.name} />
                </div>

                <div>
                    <Label htmlFor="description">{t("Description")}</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData("description", e.target.value)}
                        placeholder={t("Enter Description")}
                        rows={3}
                    />
                    <InputError message={errors.description} />
                </div>

                <div>
                    <Label htmlFor="is_active">{t("Is Active")}</Label>
                    <Select
                        value={data.is_active ? "1" : "0"}
                        onValueChange={(value) =>
                            setData("is_active", value === "1")
                        }
                    >
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="1">{t("Active")}</SelectItem>
                            <SelectItem value="0">{t("Inactive")}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.is_active} />
                </div>

                <div className="flex justify-end gap-2">
                    <Button type="button" variant="outline" onClick={onSuccess}>
                        {t("Cancel")}
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? t("Updating...") : t("Update")}
                    </Button>
                </div>
            </form>
        </DialogContent>
    );
}
