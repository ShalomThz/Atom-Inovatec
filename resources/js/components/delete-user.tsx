import * as React from 'react';
import { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import InputError from './input-error';

const DeleteUser: React.FC = () => {
    const [isOpen, setIsOpen] = useState(false);
    const { data, setData, delete: destroy, processing, errors, reset } = useForm({
        password: '',
    });

    const deleteUser = (e: React.FormEvent) => {
        e.preventDefault();

        destroy(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => reset('password'),
        });
    };

    const closeModal = () => {
        setIsOpen(false);
        reset('password');
    };

    return (
        <>
            <Button variant="destructive" onClick={() => setIsOpen(true)}>
                Eliminar Cuenta
            </Button>

            {isOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
                        <h2 className="text-lg font-semibold mb-4">¿Estás seguro de que deseas eliminar tu cuenta?</h2>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente.
                            Por favor, ingresa tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.
                        </p>

                        <form onSubmit={deleteUser}>
                            <div className="mb-4">
                                <Label htmlFor="password">Contraseña</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="Contraseña"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex justify-end gap-2">
                                <Button type="button" variant="outline" onClick={closeModal}>
                                    Cancelar
                                </Button>
                                <Button type="submit" variant="destructive" disabled={processing}>
                                    Eliminar Cuenta
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </>
    );
};

export default DeleteUser;
