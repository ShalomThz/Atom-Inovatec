import * as React from 'react';
import { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import InputError from './input-error';

interface TwoFactorSetupModalProps {
    qrCodeSvg: string;
    isOpen: boolean;
    onClose: () => void;
}

const TwoFactorSetupModal: React.FC<TwoFactorSetupModalProps> = ({ qrCodeSvg, isOpen, onClose }) => {
    const { data, setData, post, processing, errors } = useForm({
        code: '',
    });

    const confirmTwoFactor = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('two-factor.confirm'), {
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div className="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
                <h2 className="text-lg font-semibold mb-4">Configurar Autenticación de Dos Factores</h2>

                <div className="mb-4">
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Escanea el siguiente código QR con tu aplicación de autenticación.
                    </p>
                    <div className="flex justify-center mb-4" dangerouslySetInnerHTML={{ __html: qrCodeSvg }} />
                </div>

                <form onSubmit={confirmTwoFactor}>
                    <div className="mb-4">
                        <Label htmlFor="code">Código de Confirmación</Label>
                        <Input
                            id="code"
                            type="text"
                            value={data.code}
                            onChange={(e) => setData('code', e.target.value)}
                            placeholder="123456"
                        />
                        <InputError message={errors.code} />
                    </div>

                    <div className="flex justify-end gap-2">
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            Confirmar
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default TwoFactorSetupModal;
