import { Link } from '@inertiajs/react';
import { ArrowUpRight, LucideIcon } from 'lucide-react';

interface Shortcut {
   icon: LucideIcon;
   title: string;
   route: string;
}

interface DashboardShortcutProps {
   shortcuts: Shortcut[];
}

export default function DashboardShortcut({ shortcuts }: DashboardShortcutProps) {
   return (
      <div className="flex flex-col w-full h-full rounded-radius p-6 bg-digital-blue-50 border border-border">
            <div className='w-fit hover:rotate-45 transition-transform duration-200 mb-1 text-darktext'>
               <ArrowUpRight />
            </div>
            <h3 className="text-xl font-bold text-darktext mb-2">Atalhos Rápidos</h3>
            <div className="flex items-center gap-1cursor-pointer" >
               <div className="flex flex-col gap-2 w-full text-digital-blue-800">
                  <div className="flex w-full gap-4">
                     {shortcuts.map((shortcut) => (
                     <Link 
                        key={shortcut.title}
                        href={shortcut.route}
                        title={shortcut.title}
                        className="p-3 bg-digital-blue-100 border-1 rounded-radius hover:bg-digital-blue-200/70 cursor-pointer transition-colors w-full flex justify-center gap-2 items-center"
                     > 
                        <shortcut.icon />
                        <p className="font-medium">{shortcut.title}</p>
                     </Link>
                     ))}
                  </div>
            </div>
         </div>
      </div>
   );
}