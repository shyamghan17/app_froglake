import { useState } from "react";
import { Head, usePage } from "@inertiajs/react";
import { useForm } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent } from "@/components/ui/tabs";
import { Label } from "@/components/ui/label";
import InputError from "@/components/ui/input-error";
import { Input } from "@/components/ui/input";
import { RichTextEditor } from "@/components/ui/rich-text-editor";
import MediaPicker from "@/components/MediaPicker";
import { DatePicker } from "@/components/ui/date-picker";
import { TagsInput } from "@/components/ui/tags-input";
import { Switch } from "@/components/ui/switch";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Plus, Trash2 } from "lucide-react";
import { Repeater } from "@/components/ui/repeater";
import { CreatePortfolioFormData } from "./types";

export default function Create() {
    const { portfoliocategories } = usePage<any>().props;
    const [activeTab, setActiveTab] = useState("developer");


    const { t } = useTranslation();
    const { data, setData, post, processing, errors } =
        useForm<CreatePortfolioFormData>({
            // Personal Information
            photo: "",
            name: "",
            role: "",
            experience_years: "",
            email: "",
            education: "",

            // Work Details
            category_id: "",
            title: "",
            description: "",
            live_url: "",
            repository_url: "",
            skills: "",
            client: "",
            duration: "",
            team_size: "",
            start_date: "",
            end_date: "",
            budget: "",
            industry: "",

            // Work Overview
            show_overview: true,
            overview: "",

            // Gallery
            images: [] as string[],
            video_link: "",
            show_gallery: true,

            // Contact Section
            contact_heading: "",
            contact_message: "",
            show_contact: true,

            // Custom Sections
            custom_sections: [{ title: "", content: "" }],
        });



    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route("portfolio.portfolios.store"));
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t("Portfolio") },
                { label: t("Portfolios"), url: route("portfolio.portfolios.index") },
                { label: t("Create Portfolio") },
            ]}
            pageTitle={t("Create Portfolio")}
        >
            <Head title={t("Create Portfolio")} />

            <form onSubmit={submit} className="w-full">
                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">

                    <TabsContent value="developer" className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-xl font-semibold">{t("Personal Information")}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="name">{t("Name")}</Label>
                                        <Input
                                            id="name"
                                            value={data.name}
                                            onChange={(e) => setData("name", e.target.value)}
                                            placeholder={t("Enter Name")}
                                            required
                                        />
                                        <InputError message={errors.name} />
                                    </div>
                                    <div>
                                        <Label htmlFor="role">{t("Role")}</Label>
                                        <Input
                                            id="role"
                                            value={data.role}
                                            onChange={(e) => setData("role", e.target.value)}
                                            placeholder={t("Enter Role")}
                                            required
                                        />
                                        <InputError message={errors.role} />
                                    </div>
                                    <div>
                                        <Label htmlFor="email">{t("Email")}</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData("email", e.target.value)}
                                            placeholder={t("Enter Email")}
                                            required
                                        />
                                        <InputError message={errors.email} />
                                    </div>
                                    <div>
                                        <Label htmlFor="experience_years">{t("Experience Years")}</Label>
                                        <Input
                                            id="experience_years"
                                            type="number"
                                            step="0.1"
                                            value={data.experience_years}
                                            onChange={(e) => setData("experience_years", e.target.value)}
                                            placeholder={t("Enter Experience Years")}
                                        />
                                        <InputError message={errors.experience_years} />
                                    </div>
                                    <div>
                                        <MediaPicker
                                            label={t("Photo")}
                                            value={data.photo}
                                            onChange={(value) => setData("photo", Array.isArray(value) ? value[0] || "" : value)}
                                            placeholder={t("Upload Photo...")}
                                            showPreview={true}
                                            multiple={false}
                                        />
                                        <InputError message={errors.photo} />
                                    </div>
                                    <div>
                                        <Label htmlFor="education">{t("Education")}</Label>
                                        <Input
                                            id="education"
                                            value={data.education}
                                            onChange={(e) => setData("education", e.target.value)}
                                            placeholder={t("Enter Education")}
                                        />
                                        <InputError message={errors.education} />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                        <div className="flex justify-between pt-6 border-t">
                            <Button type="button" variant="outline" disabled>
                                {t("Previous")}
                            </Button>
                            <Button
                                type="button"
                                onClick={() => setActiveTab("project")}
                                disabled={!data.name || !data.role || !data.email}
                            >
                                {t("Next")}
                            </Button>
                        </div>
                    </TabsContent>

                    <TabsContent value="project" className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-xl font-semibold">{t("Work Details")}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="title">{t("Title")}</Label>
                                        <Input
                                            id="title"
                                            value={data.title}
                                            onChange={(e) => setData("title", e.target.value)}
                                            placeholder={t("Enter Title")}
                                            required
                                        />
                                        <InputError message={errors.title} />
                                    </div>
                                    <div>
                                        <Label htmlFor="description">{t("Description")}</Label>
                                        <Input
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData("description", e.target.value)}
                                            placeholder={t("Enter Description")}
                                            required
                                        />
                                        <InputError message={errors.description} />
                                    </div>
                                    <div>
                                        <Label htmlFor="category_id" required>{t("Category")}</Label>
                                        <Select
                                            value={data.category_id?.toString() || ""}
                                            onValueChange={(value) => setData("category_id", value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder={t("Select Category")} />
                                            </SelectTrigger>
                                            <SelectContent searchable={true}>
                                                {portfoliocategories.map((item: any) => (
                                                    <SelectItem key={item.id} value={item.id.toString()}>
                                                        {item.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.category_id} />
                                    </div>
                                    <div>
                                        <Label htmlFor="client">{t("Client")}</Label>
                                        <Input
                                            id="client"
                                            value={data.client}
                                            onChange={(e) => setData("client", e.target.value)}
                                            placeholder={t("Enter Client")}
                                            required
                                        />
                                        <InputError message={errors.client} />
                                    </div>
                                    <div>
                                        <Label htmlFor="live_url">{t("Live URL")}</Label>
                                        <Input
                                            id="live_url"
                                            value={data.live_url}
                                            onChange={(e) => setData("live_url", e.target.value)}
                                            placeholder={t("Enter Live URL")}
                                        />
                                        <InputError message={errors.live_url} />
                                    </div>
                                    <div>
                                        <Label htmlFor="repository_url">{t("Repository URL")}</Label>
                                        <Input
                                            id="repository_url"
                                            value={data.repository_url}
                                            onChange={(e) => setData("repository_url", e.target.value)}
                                            placeholder={t("Enter Repository URL")}
                                        />
                                        <InputError message={errors.repository_url} />
                                    </div>
                                    <div>
                                        <Label htmlFor="duration">{t("Duration")}</Label>
                                        <Input
                                            id="duration"
                                            value={data.duration}
                                            onChange={(e) => setData("duration", e.target.value)}
                                            placeholder={t("Enter Duration")}
                                        />
                                        <InputError message={errors.duration} />
                                    </div>
                                    <div>
                                        <Label htmlFor="team_size">{t("Team Size")}</Label>
                                        <Input
                                            id="team_size"
                                            type="number"
                                            value={data.team_size}
                                            onChange={(e) => setData("team_size", e.target.value)}
                                            placeholder={t("Enter Team Size")}
                                        />
                                        <InputError message={errors.team_size} />
                                    </div>
                                    <div>
                                        <Label>{t("Timeline")}</Label>
                                        <div className="grid grid-cols-2 gap-2">
                                            <DatePicker
                                                value={data.start_date}
                                                onChange={(date) => setData("start_date", date)}
                                                placeholder={t("Start Date")}
                                            />
                                            <DatePicker
                                                value={data.end_date}
                                                onChange={(date) => setData("end_date", date)}
                                                placeholder={t("End Date")}
                                            />
                                        </div>
                                        <InputError message={errors.start_date} />
                                        <InputError message={errors.end_date} />
                                    </div>
                                    <div>
                                        <Label htmlFor="budget">{t("Budget")}</Label>
                                        <Input
                                            id="budget"
                                            value={data.budget}
                                            onChange={(e) => setData("budget", e.target.value)}
                                            placeholder={t("Enter Budget")}
                                        />
                                        <InputError message={errors.budget} />
                                    </div>
                                    <div>
                                        <Label>{t("Skills")}</Label>
                                        <TagsInput
                                            value={data.skills || []}
                                            onChange={(value) => setData("skills", value)}
                                            placeholder={t("Add Skills")}
                                        />
                                        <InputError message={errors.skills} />
                                    </div>
                                    <div>
                                        <Label htmlFor="industry">{t("Industry")}</Label>
                                        <Input
                                            id="industry"
                                            value={data.industry}
                                            onChange={(e) => setData("industry", e.target.value)}
                                            placeholder={t("Enter Industry")}
                                        />
                                        <InputError message={errors.industry} />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                        <div className="flex justify-between pt-6 border-t">
                            <Button type="button" variant="outline" onClick={() => setActiveTab("developer")}>
                                {t("Previous")}
                            </Button>
                            <Button
                                type="button"
                                onClick={() => setActiveTab("overview")}
                                disabled={!data.title || !data.description || !data.client || !data.category_id}
                            >
                                {t("Next")}
                            </Button>
                        </div>
                    </TabsContent>

                    <TabsContent value="overview" className="space-y-6">
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <CardTitle className="text-xl font-semibold">{t("Work Overview")}</CardTitle>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="show_overview"
                                            checked={data.show_overview}
                                            onCheckedChange={(checked) => setData("show_overview", checked)}
                                        />
                                        <Label htmlFor="show_overview" className="cursor-pointer">
                                            {t("Show Overview")}
                                        </Label>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div>
                                    <Label htmlFor="overview">{t("Overview")}</Label>
                                    <RichTextEditor
                                        content={data.overview}
                                        onChange={(content) => setData("overview", content)}
                                        placeholder={t("Enter Overview")}
                                    />
                                    <InputError message={errors.overview} />
                                    <InputError message={errors.show_overview} />
                                </div>
                            </CardContent>
                        </Card>
                        <div className="flex justify-between pt-6 border-t">
                            <Button type="button" variant="outline" onClick={() => setActiveTab("project")}>
                                {t("Previous")}
                            </Button>
                            <Button type="button" onClick={() => setActiveTab("custom")}>
                                {t("Next")}
                            </Button>
                        </div>
                    </TabsContent>

                    <TabsContent value="custom" className="space-y-6">
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <CardTitle className="text-xl font-semibold">{t("Gallery")}</CardTitle>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="show_gallery"
                                            checked={data.show_gallery}
                                            onCheckedChange={(checked) => setData("show_gallery", checked)}
                                        />
                                        <Label htmlFor="show_gallery" className="cursor-pointer">
                                            {t("Show Gallery")}
                                        </Label>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                <div className="space-y-6">
                                    <div>
                                        <MediaPicker
                                            label={t("Images")}
                                            value={data.images}
                                            onChange={(value) => setData("images", Array.isArray(value) ? value : [value])}
                                            placeholder={t("Upload Images")}
                                            showPreview={true}
                                            multiple={true}
                                        />
                                        <InputError message={errors.images} />
                                    </div>
                                    <div>
                                        <Label htmlFor="video_link">{t("Video Link")}</Label>
                                        <Input
                                            id="video_link"
                                            value={data.video_link}
                                            onChange={(e) => setData("video_link", e.target.value)}
                                            placeholder={t("Enter Video URL")}
                                        />
                                        <InputError message={errors.video_link} />
                                    </div>
                                    <InputError message={errors.show_gallery} />
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <CardTitle className="text-xl font-semibold">{t("Contact Section")}</CardTitle>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="show_contact"
                                            checked={data.show_contact}
                                            onCheckedChange={(checked) => setData("show_contact", checked)}
                                        />
                                        <Label htmlFor="show_contact" className="cursor-pointer">
                                            {t("Show Contact")}
                                        </Label>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <Label htmlFor="contact_heading">{t("Heading")}</Label>
                                        <Input
                                            id="contact_heading"
                                            value={data.contact_heading}
                                            onChange={(e) => setData("contact_heading", e.target.value)}
                                            placeholder={t("Enter Heading")}
                                        />
                                        <InputError message={errors.contact_heading} />
                                    </div>
                                    <div>
                                        <Label htmlFor="contact_message">{t("Message")}</Label>
                                        <Input
                                            id="contact_message"
                                            value={data.contact_message}
                                            onChange={(e) => setData("contact_message", e.target.value)}
                                            placeholder={t("Enter Message")}
                                        />
                                        <InputError message={errors.contact_message} />
                                    </div>
                                </div>
                                <InputError message={errors.show_contact} />
                            </CardContent>
                        </Card>
                        <div className="flex justify-between pt-6 border-t">
                            <Button type="button" variant="outline" onClick={() => setActiveTab("overview")}>
                                {t("Previous")}
                            </Button>
                            <Button
                                type="button"
                                onClick={() => setActiveTab("dynamic")}
                                disabled={false}
                            >
                                {t("Next")}
                            </Button>
                        </div>
                    </TabsContent>

                    <TabsContent value="dynamic" className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-xl font-semibold">{t("Custom Sections")}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Repeater
                                    fields={[
                                        {
                                            name: "title",
                                            label: t("Title"),
                                            type: "text",
                                            placeholder: t("Section Title"),
                                            required: true,
                                            layout: { colSpan: 1 },
                                        },
                                        {
                                            name: "content",
                                            label: t("Content"),
                                            type: "richtext",
                                            placeholder: t("Section Content"),
                                            required: true,
                                            layout: { colSpan: 1 },
                                        },
                                    ]}
                                    value={data.custom_sections.map((section, index) => ({
                                        id: `section-${index}`,
                                        title: section.title || "",
                                        content: section.content || "",
                                    }))}
                                    onChange={(items) => {
                                        const sections = items.map(({ id, ...item }) => item);
                                        setData("custom_sections", sections);
                                    }}
                                    layout={{
                                        type: "grid",
                                        columns: 1,
                                        gap: "4",
                                    }}
                                    addButtonText={t("Add Section")}
                                    deleteTooltipText={t("Remove")}
                                    minItems={0}
                                    showDefault={false}
                                />
                            </CardContent>
                        </Card>
                        <div className="flex justify-between pt-6 border-t">
                            <Button type="button" variant="outline" onClick={() => setActiveTab("custom")}>
                                {t("Previous")}
                            </Button>
                            <div className="flex gap-2">
                                <Button type="button" variant="outline" onClick={() => window.history.back()}>
                                    {t("Cancel")}
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing ? t("Creating...") : t("Create")}
                                </Button>
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>
            </form>
        </AuthenticatedLayout>
    );
}
