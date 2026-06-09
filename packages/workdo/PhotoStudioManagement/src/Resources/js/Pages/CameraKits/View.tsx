import { DialogContent, DialogTitle } from '@/components/ui/dialog';
import { useTranslation } from 'react-i18next';
import { Camera, Layers, CheckCircle, FileText, Tag, Boxes } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { PhotoStudioCameraKit } from './types';
import { getImagePath } from '@/utils/helpers';

interface ViewProps {
    cameraKit: PhotoStudioCameraKit;
    equipmentTags: Array<{ id: number; name: string }>;
    onClose: () => void;
}

export default function View({ cameraKit, equipmentTags }: ViewProps) {
    const { t } = useTranslation();

    const imagePreview = cameraKit.image
        ? (cameraKit.image.startsWith('http') ? cameraKit.image : getImagePath(cameraKit.image))
        : null;

    const resolvedTags = (cameraKit.tags || [])
        .map(tagId => equipmentTags.find(t => t.id.toString() === tagId))
        .filter(Boolean) as Array<{ id: number; name: string }>;

    return (
        <DialogContent className="max-w-2xl p-0 overflow-hidden max-h-[90vh] overflow-y-auto">

            {/* Top section: col-8 image | col-4 info */}
            <div className="grid grid-cols-12">

                {/* Image — col-8 */}
                <div className="col-span-7 h-56 m-3 p-1.5">
                    <div className="relative w-full h-full rounded-md overflow-hidden bg-gray-100">
                        {imagePreview ? (
                            <img src={imagePreview} alt={cameraKit.name} className="w-full h-full object-cover" />
                        ) : (
                            <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <Camera className="w-16 h-16 text-gray-300" />
                            </div>
                        )}
                        <div className="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent" />
                        <span className={`absolute top-3 left-3 text-xs  px-2 py-1 rounded-full ${cameraKit.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                            {cameraKit.status === 'available' ? t('Available') : t('Unavailable')}
                        </span>
                        <div className="absolute bottom-4 left-4 right-4">
                            <DialogTitle className="text-lg font-bold text-white leading-tight truncate">
                                {cameraKit.name}
                            </DialogTitle>
                            <div className="flex items-center gap-1.5 mt-1">
                                <Layers className="w-3.5 h-3.5 text-white/60" />
                                <span className="text-sm text-white/70">{cameraKit.equipment_type?.name || '-'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right panel — col-4 */}
                <div className="col-span-5 border-l border-gray-100 px-4 py-4 space-y-4 bg-white">

                    {/* Kit Name */}
                    <div className="flex items-start gap-2.5">
                        <div className="w-7 h-7 rounded-lg  flex items-center justify-center shrink-0 mt-0.5">
                            <Layers className="w-3.5 h-3.5 text-blue-500" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-sm font-medium  mb-0.5">{t('Name')}</p>
                            <p className="text-sm text-gray-700 leading-relaxed">{cameraKit.name}</p>
                        </div>
                        
                    </div>
                      <div className="flex items-start gap-2.5">
                        <div className="w-7 h-7 rounded-lg  flex items-center justify-center shrink-0 mt-0.5">
                            <Boxes className="w-3.5 h-3.5 text-yellow-500" />
                        </div>
                        <div className="min-w-0">
                            <p className="text-sm font-medium  mb-0.5">{t('Type')}</p>
                            <p className="text-sm text-gray-700 leading-relaxed">{cameraKit.equipment_type?.name || '-'}</p>
                        </div>
                        
                    </div>


                    {/* Tags */}
                    <div className="flex items-start gap-2.5">
                        <div className="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <Tag className="w-3.5 h-3.5 text-purple-500" />
                        </div>
                        <div>
                            <p className="text-sm font-medium  mb-1.5">{t('Tags')}</p>
                            <div className="flex flex-wrap gap-1">
                                {resolvedTags.length > 0
                                    ? resolvedTags.map((tag, i) => (
                                        <Badge key={i} variant="secondary" className="text-xs">{tag.name}</Badge>
                                    ))
                                    : <span className="text-xs text-gray-400">-</span>
                                }
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {/* Body */}
            <div className=" py-4 space-y-5 border-t border-gray-100">

                {/* Description */}
                {cameraKit.description && (
                    <div className="flex items-start gap-3">
                        <div className="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <FileText className="w-3.5 h-3.5 text-orange-500" />
                        </div>
                        <div>
                            <p className="text-sm font-medium mb-1">{t('Description')}</p>
                            <p className="text-sm text-gray-700 leading-relaxed">{cameraKit.description}</p>
                        </div>
                    </div>
                )}

                {/* Specifications */}
                {cameraKit.specifications && cameraKit.specifications.length > 0 && (
                    <div className="flex items-start gap-3">
                        <div className="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <CheckCircle className="w-3.5 h-3.5 text-indigo-500" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium mb-2">{t('Specifications')}</p>
                            <div className="rounded-xl border border-gray-200 overflow-hidden">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="bg-gray-50 border-b border-gray-200">
                                            <th className="text-left px-4 py-2.5 text-xs font-bold text-gray-600 w-2/5">{t('Field')}</th>
                                            <th className="text-left px-4 py-2.5 text-xs font-bold text-gray-600">{t('Value')}</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100">
                                        {cameraKit.specifications.map((spec, i) => (
                                            <tr key={i} className="hover:bg-gray-50/60 transition-colors">
                                                <td className="px-4 py-2.5 text-xs font-bold text-gray-700">{spec.field_name}</td>
                                                <td className="px-4 py-2.5 text-xs text-gray-600">{spec.description}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                )}

            </div>
        </DialogContent>
    );
}
