import * as React from 'react';
import { Button } from './ui/button';

interface TwoFactorRecoveryCodesProps {
    recoveryCodes: string[];
    onRegenerate?: () => void;
}

const TwoFactorRecoveryCodes: React.FC<TwoFactorRecoveryCodesProps> = ({ recoveryCodes, onRegenerate }) => {
    const copyToClipboard = () => {
        navigator.clipboard.writeText(recoveryCodes.join('\n'));
    };

    return (
        <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h4 className="font-semibold mb-2">Códigos de Recuperación</h4>
            <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Guarda estos códigos de recuperación en un lugar seguro. Pueden ser usados para recuperar el acceso a tu cuenta si pierdes tu dispositivo de autenticación de dos factores.
            </p>

            <div className="bg-white dark:bg-gray-900 rounded p-3 mb-4 font-mono text-sm">
                {recoveryCodes.map((code, index) => (
                    <div key={index}>{code}</div>
                ))}
            </div>

            <div className="flex gap-2">
                <Button onClick={copyToClipboard} variant="outline">
                    Copiar Códigos
                </Button>
                {onRegenerate && (
                    <Button onClick={onRegenerate} variant="outline">
                        Regenerar Códigos
                    </Button>
                )}
            </div>
        </div>
    );
};

export default TwoFactorRecoveryCodes;
