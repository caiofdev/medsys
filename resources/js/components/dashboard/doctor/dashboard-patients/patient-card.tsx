import { Avatar } from '@radix-ui/react-avatar';
import {  ClipboardList } from 'lucide-react';

export interface PatientCardProps {
   isDoctors?: boolean;
   patient: {
      name: string;
      birth_date: string;
      email: string;
   };
}

export default function PatientCard({ patient, isDoctors }: PatientCardProps) {
   const patientAge = new Date().getFullYear() - new Date(patient.birth_date).getFullYear();
   return (
      <div className="flex bg-digital-blue-100 rounded-radius px-4 py-3 items-center border border-border justify-between">
         <div className="flex items-center gap-4">
            <Avatar className="h-10 w-10 rounded-full bg-digital-blue-400 flex items-center justify-center text-white font-semibold text-lg shadow">
               {patient.name.charAt(0)}
            </Avatar>
            <div className="flex flex-col justify-center gap-0.5">
               <span className="text-digital-blue-900 font-semibold text-base leading-tight">{patient.name}</span>
               <span className="text-digital-blue-700 text-xs font-medium">{patientAge} anos</span>
               <span className="text-gray-500 text-xs font-mono truncate max-w-[160px]">{patient.email}</span>
            </div>
         </div>
         {isDoctors && (
         <button
            className="flex items-center justify-center rounded-full p-2 bg-digital-blue-50 text-digital-blue-700 cursor-pointer"
            title="Ver prontuário"
         >
            <ClipboardList size={20} />
         </button>
         )}
      </div>
   );
}